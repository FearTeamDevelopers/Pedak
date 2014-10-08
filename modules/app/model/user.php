<?php

use THCFrame\Model\Model;
use THCFrame\Security\UserInterface;

/**
 * Description of App_Model_User
 *
 * @author Tomy
 */
class App_Model_User extends Model implements UserInterface
{

    /**
     * @readwrite
     */
    protected $_alias = 'us';
    
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
     * @type text
     * @length 60
     * @index
     * @unique
     *
     * @validate required, email, max(60)
     * @label email address
     */
    protected $_email;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 200
     * @index
     *
     * @validate required, min(5), max(200)
     * @label password
     */
    protected $_password;

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
     * @length 25
     * 
     * @validate required, alpha, max(25)
     * @label user role
     */
    protected $_role;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 40
     * @unique
     *
     * @validate required, max(40)
     */
    protected $_salt;

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
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_lastLogin;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 30
     *
     * @validate numeric, max(30)
     * @label account lockdown time
     */
    protected $_loginLockdownTime;

    /**
     * @column
     * @readwrite
     * @type tinyint
     *
     * @validate numeric, max(2)
     * @label login attemp counter
     */
    protected $_loginAttempCounter;

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
     * @param type $datetime
     */
    public function setLastLogin($datetime)
    {
        $this->_lastLogin = $datetime;
    }
    
    /**
     * 
     * @param type $value
     * @throws \THCFrame\Security\Exception\Role
     */
    public function setRole($value)
    {
        $role = strtolower(substr($value, 0, 5));
        if ($role != 'role_') {
            throw new \THCFrame\Security\Exception\Role(sprintf('Role %s is not valid', $value));
        } else {
            $this->_role = $value;
        }
    }

    /**
     * 
     */
    public function isActive()
    {
        return (boolean) $this->_active;
    }

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
        if ($type) {
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
        if ($type) {
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
