<?php

use THCFrame\Security\Model\BasicUser;

/**
 * Description of App_Model_User
 *
 * @author Tomy
 */
class App_Model_User extends BasicUser
{

    /**
     * @column
     * @readwrite
     * @type text
     * @length 40
     *
     * @validate required, alpha, min(3), max(40)
     * @label first name
     */
    protected $_firstname;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 40
     *
     * @validate required, alpha, min(3), max(40)
     * @label last name
     */
    protected $_lastname;

    /**
     * @column
     * @readwrite
     * @type text
     * @lenght 12
     *
     * @validate required, date, max(12)
     * @label date of birth
     */
    protected $_dob;

    /**
     * @column
     * @readwrite
     * @type text
     * @lenght 2
     * 
     * @validate required, numeric, max(2)
     * @label player number
     */
    protected $_playerNum;

    /**
     * @column
     * @readwrite
     * @type text
     * @lenght 15
     * 
     * @validate required, numeric, max(15)
     * @label CFBU player id
     */
    protected $_cfbuPersonalNum;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 2
     * 
     * @validate required, alpha, max(2)
     * @label team
     */
    protected $_team;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 30
     * 
     * @validate alphanumeric, max(30)
     * @label nickname
     */
    protected $_nickname;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 250
     * 
     * @validate path, max(250)
     * @label photo
     */
    protected $_photoMain;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 250
     * 
     * @validate path, max(250)
     * @label photo thumb
     */
    protected $_photoThumb;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 20
     * 
     * @validate required, alpha, max(20)
     * @label position
     */
    protected $_position;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 2
     * 
     * @validate required, alpha, max(2)
     * @label grip
     */
    protected $_grip;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     * 
     * @validate alphanumeric, max(5000)
     * @label other
     */
    protected $_other;



    /**
     * 
     * @return type
     */
    public function getWholeName()
    {
        return $this->_firstname . " " . $this->_lastname;
    }

    /**
     * 
     * @return type
     */
    public function __toString()
    {
        $str = "Id: {$this->_id} <br/>Email: {$this->_email} <br/> Name: {$this->_firstname} {$this->_lastname}";
        return $str;
    }

    /**
     * 
     * @return type
     */
    public function getUnlinkPath($type = true)
    {
        if ($type && !empty($this->_photoMain)) {
            if (file_exists($this->_photoMain)) {
                return $this->_photoMain;
            } elseif (file_exists('.' . $this->_photoMain)) {
                return '.' . $this->_photoMain;
            } elseif (file_exists('./' . $this->_photoMain)) {
                return './' . $this->_photoMain;
            }
        } else {
            return $this->_photoMain;
        }
    }

    /**
     * 
     * @return type
     */
    public function getUnlinkThumbPath($type = true)
    {
        if ($type && !empty($this->_photoThumb)) {
            if (file_exists($this->_photoThumb)) {
                return $this->_photoThumb;
            } elseif (file_exists('.' . $this->_photoThumb)) {
                return '.' . $this->_photoThumb;
            } elseif (file_exists('./' . $this->_photoThumb)) {
                return './' . $this->_photoThumb;
            }
        } else {
            return $this->_photoThumb;
        }
    }
    
}
