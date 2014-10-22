<?php

use THCFrame\Model\Model as Model;

/**
 * Description of App_Model_ChatTopic
 *
 * @author Tomy
 */
class App_Model_ChatTopic extends Model
{

    /**
     * @readwrite
     */
    protected $_alias = 'cht';

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
     * @type integer
     * 
     * @validate numeric, max(8)
     * @label user
     */
    protected $_userId;

    /**
     * @column
     * @readwrite
     * @type boolean
     * 
     * @validate max(3)
     */
    protected $_status;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, alphanumeric, max(100)
     * @label title
     */
    protected $_title;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 200
     *
     * @validate required, max(200)
     * @label url key
     */
    protected $_urlKey;

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
            $this->setActive(true);
        }
        $this->setModified(date("Y-m-d H:i:s"));
    }

    /**
     * 
     * @return type
     */
    public static function fetchAll()
    {
        $query = self::getQuery(array('cht.*'))
                ->join('tb_user', 'us.id = cht.userId', 'us', 
                        array('us.firstname', 'us.lastname'));
        
        $topics = self::initialize($query);
        return $topics;
    }
    
    /**
     * 
     * @return type
     */
    public static function fetchAllActive()
    {
        $query = self::getQuery(array('cht.*'))
                ->join('tb_user', 'us.id = cht.userId', 'us', 
                        array('us.firstname', 'us.lastname'))
                ->where('cht.active = ?', true);
        
        $topics = self::initialize($query);
        return $topics;
    }
    
}
