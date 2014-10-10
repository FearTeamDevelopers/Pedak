<?php

use THCFrame\Model\Model as Model;

/**
 * Description of App_Model_TrainingHost
 *
 * @author Tomy
 */
class App_Model_TrainingHost extends Model
{

    /**
     * @readwrite
     */
    protected $_alias = 'th';

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
     * @type text
     * @length 150
     * 
     * @validate required, alphanumeric, max(150)
     * @label host name
     */
    protected $_hostName;

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

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function fetchHostsByTrainingId($id)
    {
        $hosts = new self(array('trainingId' => (int)$id));
        return $hosts->getHostsByTrainingId();
    }
    
    /**
     * 
     * @return type
     */
    public function getHostsByTrainingId()
    {
        return self::all(array('trainingId = ?' => $this->getTrainingId()));
    }
}
