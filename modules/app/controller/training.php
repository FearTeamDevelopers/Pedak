<?php

use App\Etc\Controller as Controller;
use THCFrame\Request\RequestMethods;
use THCFrame\Registry\Registry;


/**
 * Description of App_Controller_Training
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
                ->set('canonical', $canonical)
                ->set('activemenu', 'training');
        
        $trainings = App_Model_Training::fetchAllLimited();
        
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
                    'trainingId = ?' => $id
        ));

        if (NULL !== $attend) {
            $attend->status = $status;

            if ($attend->validate()) {
                $attend->save();

                $view->successMessage('Your choice has been successfully saved');
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

                $view->successMessage('Your choice has been successfully saved');
                self::redirect('/treninky');
            }
        }
    }
    
    /**
     * 
     * @before _secured, _member
     * @param type $id
     */
    public function addHost($id)
    {
        $view = $this->getActionView();
        $view->set('submstoken', $this->mutliSubmissionProtectionToken());
        
        if(RequestMethods::post('submitAddHost')){
            if($this->checkCSRFToken() !== true && 
                    $this->checkMutliSubmissionProtectionToken(RequestMethods::post('submstoken')) !== true){
                self::redirect('/treninky');
            }
            
            $training = App_Model_Training::first(array('id = ?' => (int)$id));
            
            if($training === null){
                $view->warningMessage('Trenink - '.self::ERROR_MESSAGE_2);
                self::redirect('/treninky');
            }
            
            $host = new App_Model_TrainingHost(array(
                'userId' => $this->getUser()->getId(),
                'trainingId' => (int) $id,
                'hostName' => RequestMethods::post('hostname')
            ));
            
            if($host->validate()){
                $host->save();
                
                $view->successMessage('Host byl přidán');
                self::redirect('/treninky');
            }else{
                $view->errorMessage(self::ERROR_MESSAGE_1);
            }
        }
    }
    
    /**
     * 
     * @before _secured, _member
     * @param type $id
     * @return type
     */
    public function deleteHost($id)
    {
        $view = $this->getActionView();

        $host = App_Model_TrainingHost::first(array('id = ?' => (int) $id));

        if ($host !== null) {
            if ($host->delete()) {
                $view->successMessage('Host'.self::SUCCESS_MESSAGE_3);
                self::redirect('/treninky');
            } else {
                $view->warningMessage(self::ERROR_MESSAGE_1);
                self::redirect('/treninky');
            }
        } else {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/treninky');
        }
    }

}
