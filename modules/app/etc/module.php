<?php

use THCFrame\Module\Module as Module;

/**
 * Description of Module
 *
 * @author Tomy
 */
class App_Etc_Module extends Module{

    /**
     * @read
     */
    protected $_moduleName = "App";
    
    /**
     * @read 
     */
    protected $_observerClass = '';
    
    protected $_routes = array(
        array(
            'pattern' => '/login',
            'module' => 'app',
            'controller' => 'user',
            'action' => 'login',
        ),
        array(
            'pattern' => '/logout',
            'module' => 'app',
            'controller' => 'user',
            'action' => 'logout',
        ),
        array(
            'pattern' => '/register/:key',
            'module' => 'app',
            'controller' => 'user',
            'action' => 'register',
            'args' => ':key'
        ),
        array(
            'pattern' => '/team',
            'module' => 'app',
            'controller' => 'index',
            'action' => 'team',
        ),
        array(
            'pattern' => '/profil',
            'module' => 'app',
            'controller' => 'user',
            'action' => 'edit',
        ),
        array(
            'pattern' => '/kecarna',
            'module' => 'app',
            'controller' => 'index',
            'action' => 'chat',
        ),
        array(
            'pattern' => '/treninky',
            'module' => 'app',
            'controller' => 'training',
            'action' => 'index',
        ),
        array(
            'pattern' => '/zapasy',
            'module' => 'app',
            'controller' => 'match',
            'action' => 'index',
        ),
        array(
            'pattern' => '/kontakt',
            'module' => 'app',
            'controller' => 'index',
            'action' => 'contact',
        ),
        array(
            'pattern' => '/novinky/:urlkey',
            'module' => 'app',
            'controller' => 'news',
            'action' => 'detail',
            'args' => ':urlkey'
        ),
        array(
            'pattern' => '/zapasy/detail/:id',
            'module' => 'app',
            'controller' => 'match',
            'action' => 'detail',
            'args' => ':id'
        ),
        array(
            'pattern' => '/trenink/dochazka/:id/:status',
            'module' => 'app',
            'controller' => 'training',
            'action' => 'attend',
            'args' => array(':id',':status')
        )
        
    );
}