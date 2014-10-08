<?php

use THCFrame\Model\Model as Model;

/**
 * Description of App_Model_Training
 *
 * @author Tomy
 */
class App_Model_Training extends Model
{

    /**
     * @readwrite
     */
    protected $_alias = 'tr';

    /**
     * @column
     * @readwrite
     * @primary
     * @type auto_increment
     */
    protected $_id;

    /**
     * @column
     * @readwrite
     * @type boolean
     * @index
     * 
     * @validate max(3)
     */
    protected $_active;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 150
     * 
     * @validate required, alphanumeric, max(150)
     * @label title
     */
    protected $_title;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, alphanumeric, max(100)
     * @label location
     */
    protected $_location;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 12
     * 
     * @validate required, date, max(12)
     * @label date
     */
    protected $_startDate;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 12
     * 
     * @validate required, time, max(12)
     * @label time
     */
    protected $_startTime;

    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_created;

    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_modified;

    /**
     * @readwrite
     * @var type 
     */
    protected $_attendance;

    /**
     * 
     */
    public function preSave()
    {
        $primary = $this->getPrimaryColumn();
        $raw = $primary["raw"];

        if (empty($this->$raw)) {
            $this->setCreated(date("Y-m-d H:i:s"));
            $this->setActive(true);
        }
        $this->setModified(date("Y-m-d H:i:s"));
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function fetchAttendanceByTraining($id)
    {
        $training = new self(array('id' => (int) $id));
        return $training->getAttendanceByTraining();
    }

    /**
     * 
     * @return type
     */
    public static function fetchPercentAttendance()
    {
        $totalCount = App_Model_Training::count(array('active = ?' => true, 'startDate <= ?' => date('Y-m-d')));

        $query = App_Model_Attendance::getQuery(array('at.*', 'COUNT(at.id)' => 'cnt'))
                ->join('tb_user', 'us.id = at.userId', 'us', 
                        array('us.firstname', 'us.lastname'))
                ->join('tb_training', 'at.trainingId = tr.id', 'tr', 
                        array('tr.startDate'))
                ->where('at.status = ?', 1)
                ->where('tr.startDate <= ?', date('Y-m-d'))
                ->groupby('at.userId')
                ->order('us.lastname', 'ASC');

        $attend = App_Model_User::initialize($query);

        $ra = array();

        if ($attend !== null) {
            foreach ($attend as $value) {
                $ra[$value->firstname . ' ' . $value->lastname] = round(($value->cnt / $totalCount) * 100, 2);
            }

            return $ra;
        } else {
            return null;
        }
    }

    /**
     * 
     * @return type
     */
    public function getAttendanceByTraining()
    {
        $query = App_Model_Attendance::getQuery(array('at.*'))
                ->join('tb_user', 'at.userId = us.id', 'us', 
                        array('us.firstname', 'us.lastname'))
                ->where('at.trainingId = ?', $this->getId());

        $attendance = App_Model_Attendance::initialize($query);

        return $attendance;
    }

}
