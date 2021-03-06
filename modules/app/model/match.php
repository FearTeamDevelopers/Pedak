<?php

use THCFrame\Model\Model as Model;

/**
 * Description of App_Model_Match
 *
 * @author Tomy
 */
class App_Model_Match extends Model
{

    /**
     * @readwrite
     */
    protected $_alias = 'ma';

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
     * @length 40
     * 
     * @validate required, alphanumeric, max(40)
     * @label home
     */
    protected $_home;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 40
     * 
     * @validate required, alphanumeric, max(40)
     * @label host
     */
    protected $_away;

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
     * @validate time, max(12)
     * @label time
     */
    protected $_startTime;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 50
     * 
     * @validate required, alphanumeric, max(50)
     * @label hall
     */
    protected $_hall;

    /**
     * @column
     * @readwrite
     * @type tinyint
     * 
     * @validate numeric, max(2)
     * @label score home
     */
    protected $_scoreHome;

    /**
     * @column
     * @readwrite
     * @type tinyint
     * 
     * @validate numeric, max(2)
     * @label score host
     */
    protected $_scoreAway;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * 
     * @validate alphanumeric, max(10)
     * @label season
     */
    protected $_season;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     * 
     * @validate alphanumeric, max(2048)
     * @label report
     */
    protected $_report;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 1
     * 
     * @validate required, alpha, max(1)
     * @label team
     */
    protected $_team;

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

}
