<?php

use App\Libraries\Controller as Controller;

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
        $attendanceModel = new App_Model_Attendance();
        $connector = $attendanceModel->getConnector();
        $id = $connector->escape($id);

        $sql = "SELECT u.firstname, u.lastname, ta.* ";
        $sql .= "FROM tb_attendance ta ";
        $sql .= "JOIN tb_user u ON ta.userId = u.id ";
        $sql .= "WHERE ta.active = true AND trainingId = '{$id}'";

        $result = $connector->execute($sql);

        $rows = array();

        for ($i = 0; $i < $result->num_rows; $i++) {
            $rows[] = $result->fetch_array(MYSQLI_ASSOC);
        }

        return $rows;
    }

    /**
     * @before _secured
     */
    public function index() {
        $view = $this->getActionView();

        $trainings = App_Model_Training::all(
                        array(
                    "active = ?" => true,
                    "date >= ?" => date("Y-m-d")
                        ), array("id", "title", "date"), "date", "ASC", 6
        );

        foreach ($trainings as $key => $training) {
            $trainings[$key]->attendance = $this->_getAttendace($training->id);
        }

        $view->set("trainings", $trainings);
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
                    "userId = ?" => $userId,
                    "trainingId = ?" => $id,
                    "active = ?" => true
        ));

        if (NULL !== $attend) {
            $attend->status = $status;

            if ($attend->validate()) {
                $attend->save();

                $view->flashMessage("Your choice has been successfully saved");
                self::redirect("/treninky");
            }
        } else {
            $newAttend = new App_Model_Attendance(array(
                "userId" => $userId,
                "trainingId" => $id,
                "status" => $status
            ));

            if ($newAttend->validate()) {
                $newAttend->save();

                $view->flashMessage("Your choice has been successfully saved");
                self::redirect("/treninky");
            }
        }
    }

}