<?php

use THCFrame\Model\Model;

/**
 * Description of App_Model_Video
 *
 * @author Tomy
 */
class App_Model_Video extends Model
{

    /**
     * @readwrite
     */
    protected $_alias = 'vi';

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
     * @length 250
     * 
     * @validate required, url, max(250)
     * @label url
     */
    protected $_path;

    /**
     * @column
     * @readwrite
     * @type integer
     * 
     * @validate required, numeric, max(8)
     * @label width
     */
    protected $_width;

    /**
     * @column
     * @readwrite
     * @type integer
     * 
     * @validate required, numeric, max(8)
     * @label height
     */
    protected $_height;

    /**
     * @column
     * @readwrite
     * @type tinyint
     * 
     * @validate numeric, max(2)
     * @label priority
     */
    protected $_priority;

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
        $raw = $primary['raw'];

        if (empty($this->$raw)) {
            $this->setCreated(date('Y-m-d H:i:s'));
            $this->setActive(true);
        }
        $this->setModified(date('Y-m-d H:i:s'));
    }

}
