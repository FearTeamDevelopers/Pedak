<?php

use App\Etc\Controller as Controller;

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
