<?php

use App\Etc\Controller as Controller;
use THCFrame\Request\RequestMethods;
use THCFrame\Registry\Registry;


/**
 * Description of TrainingController
 *
 * @author Tomy
 */
class App_Controller_Training extends Controller
{

    /**
     * @before _secured
     */
    public function index()
    {
        $view = $this->getActionView();
        $host = RequestMethods::server('HTTP_HOST');
        
        $canonical = 'http://' . $host . '/treninky';

        $this->getLayoutView()->set('metatitle', 'Peďák - Tréninky')
                ->set('canonical', $canonical);
        
        $cache = Registry::get('cache');
        
        $content = $cache->get('trainings');
        
        if($content !== null){
            $trainings = $content;
        }else{
            $trainings = App_Model_Training::all(
                    array('active = ?' => true, 'startDate >= ?' => date('Y-m-d')),
                    array('*'),
                    array('startDate' => 'ASC'),
                    10
            );
            $cache->set('trainings', $trainings);
        }
        
        $view->set('trainings', $trainings);

    }

    /**
     * @before _secured
     * @param type $id
     * @param type $status
     */
    public function attend($id, $status)
    {
        $view = $this->getActionView();
        $userId = $this->getUser()->getId();

        $attend = App_Model_Attendance::first(array(
                    'userId = ?' => $userId,
                    'trainingId = ?' => $id,
                    'active = ?' => true
        ));

        if (NULL !== $attend) {
            $attend->status = $status;

            if ($attend->validate()) {
                $attend->save();

                $view->flashMessage('Your choice has been successfully saved');
                self::redirect('/treninky');
            }
        } else {
            $newAttend = new App_Model_Attendance(array(
                'userId' => $userId,
                'trainingId' => $id,
                'status' => $status
            ));

            if ($newAttend->validate()) {
                $newAttend->save();

                $view->flashMessage('Your choice has been successfully saved');
                self::redirect('/treninky');
            }
        }
    }

}
