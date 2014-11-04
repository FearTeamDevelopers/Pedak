<?php

namespace Cron\Etc;

use THCFrame\Events\Events as Events;
use THCFrame\Registry\Registry;
use THCFrame\Controller\Controller as BaseController;

/**
 * Description of Controller
 *
 * @author Tomy
 */
class Controller extends BaseController
{

    private $_security;

    /**
     * 
     * @param type $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->_security = Registry::get('security');

        $database = Registry::get('database');
        $database->connect();

        // schedule disconnect from database 
        Events::add('framework.controller.destruct.after', function($name) {
            $database = Registry::get('database');
            $database->disconnect();
        });
    }

    /**
     * @protected
     */
    public function _secured()
    {
        $session = Registry::get('session');

        $user = $this->getUser();

        if (!$user) {
            self::redirect('/login');
        }

        if (time() - $session->get('lastActive') < 600) {
            $session->set('lastActive', time());
        } else {
            $view = $this->getActionView();

            $view->flashMessage('You has been logged out for long inactivity');
            $this->_security->logout();
            self::redirect('/login');
        }
    }

    /**
     * @protected
     */
    public function _cron()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];

        if (!preg_match('/curl/i', $u_agent)) {
            exit();
        }
    }

    /**
     * load user from security context
     */
    public function getUser()
    {
        $user = $this->_security->getUser();

        return $user;
    }

    /**
     * 
     */
    public function render()
    {
        $this->setWillRenderLayoutView(false);
        $this->setWillRenderActionView(false);

        parent::render();
    }

}
