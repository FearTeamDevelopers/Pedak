<?php

use THCFrame\Module\Module as Module;

/**
 * Class for module specific settings
 *
 * @author Tomy
 */
class Admin_Etc_Module extends Module
{

    /**
     * @read
     */
    protected $_moduleName = 'Admin';

    /**
     * @read
     */
    protected $_observerClass = 'Admin_Etc_Observer';
    protected $_routes = array(
        array(
            'pattern' => '/admin/login',
            'module' => 'admin',
            'controller' => 'user',
            'action' => 'login',
        ),
        array(
            'pattern' => '/admin/logout',
            'module' => 'admin',
            'controller' => 'user',
            'action' => 'logout',
        )
    );

}
