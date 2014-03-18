<?php

use App\Etc\Controller as Controller;

/**
 * Description of TrainingController
 *
 * @author Tomy
 */
class App_Controller_Training extends Controller {

    /**
     * @protected
     * @before _secured
     * @param type $id
     */
    protected function _getAttendace($id) {
        
        $query = App_Model_Training::getQuery(array('tb_training.*'));
        
        $query->join('tb_attendance', 'tb_training.id = ta.trainingId', 'ta', array('*'));
        $query->join('tb_user', 'ta.userId = u.id', 'u', array('u.firstname' => 'firstname', 'u.lastname' => 'lastname'));
        $query->where('tb_training.active', true);
        $query->where('ta.active', $id);
        $trainings = App_Model_Training::initialize($query);
        
        return $trainings;
    }

    /**
     * @before _secured
     */
    public function index() {
        $view = $this->getActionView();

        $query = App_Model_Training::getQuery(array('tb_training.*'));
        
        $query->join('tb_attendance', 'tb_training.id = ta.trainingId', 'ta', array('*'));
        $query->join('tb_user', 'ta.userId = u.id', 'u', array('u.firstname' => 'firstname', 'u.lastname' => 'lastname'));
        $query->where('tb_training.active', true);
        $query->where('ta.active', $id);
        $trainings = App_Model_Training::initialize($query);
        
        $view->set('trainings', $trainings);
    }

    /**
     * @before _secured
     * @param type $id
     * @param type $status
     */
    public function attend($id, $status) {
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