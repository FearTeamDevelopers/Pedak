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
            'pattern' => '/admin',
            'module' => 'admin',
            'controller' => 'index',
            'action' => 'index',
        ),
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
            'pattern' => '/registrace/:key',
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
            'action' => 'profile',
        ),
        array(
            'pattern' => '/kecarna',
            'module' => 'app',
            'controller' => 'chat',
            'action' => 'index',
        ),
        array(
            'pattern' => '/kecarna/:urlKey',
            'module' => 'app',
            'controller' => 'chat',
            'action' => 'topicDetail',
            'args' => ':urlKey'
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
            'pattern' => '/novinky',
            'module' => 'app',
            'controller' => 'news',
            'action' => 'index'
        ),
        array(
            'pattern' => '/galerie',
            'module' => 'app',
            'controller' => 'gallery',
            'action' => 'index'
        ),
        array(
            'pattern' => '/galerie/:year',
            'module' => 'app',
            'controller' => 'gallery',
            'action' => 'index',
            'args' => ':year'
        ),
        array(
            'pattern' => '/galerie/r/:urlkey',
            'module' => 'app',
            'controller' => 'gallery',
            'action' => 'detail',
            'args' => ':urlkey'
        ),
        array(
            'pattern' => '/novinky/p/:page',
            'module' => 'app',
            'controller' => 'news',
            'action' => 'index',
            'args' => ':page'
        ),
        array(
            'pattern' => '/novinky/r/:urlkey',
            'module' => 'app',
            'controller' => 'news',
            'action' => 'detail',
            'args' => ':urlkey'
        ),
        array(
            'pattern' => '/zapasy/r/:id',
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