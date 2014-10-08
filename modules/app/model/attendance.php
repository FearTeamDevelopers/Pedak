<?php

use THCFrame\Model\Model as Model;

/**
 * Description of App_Model_Attendance
 *
 * @author Tomy
 */
class App_Model_Attendance extends Model
{

    /**
     * @readwrite
     */
    protected $_alias = 'at';

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
     * @type integer
     * @index
     * 
     * @validate required, numeric, max(8)
     * @label user id
     */
    protected $_userId;

    /**
     * @column
     * @readwrite
     * @type integer
     * @index
     * 
     * @validate required, numeric, max(8)
     * @label training id
     */
    protected $_trainingId;

    /**
     * @column
     * @readwrite
     * @type tinyint
     * 
     * @validate required, numeric, max(2)
     * @label status
     */
    protected $_status;

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
     * 
     */
    public function preSave()
    {
        $primary = $this->getPrimaryColumn();
        $raw = $primary["raw"];

        if (empty($this->$raw)) {
            $this->setCreated(date("Y-m-d H:i:s"));
        }
        $this->setModified(date("Y-m-d H:i:s"));
    }

}
