<?php

namespace THCFrame\Module;

use THCFrame\Core\Base;
use THCFrame\Events\Events as Event;
use THCFrame\Module\Exception;
use THCFrame\Events\SubscriberInterface;

/**
 * Application module class
 */
class Module extends Base
{

    /**
     * @read
     */
    protected $_routes = array();
    
    /**
     * @read
     */
    protected $_redirects = array();
    
    /**
     * @read
     */
    protected $_moduleName;

    /**
     * @read
     */
    protected $_observerClass = null;

    /**
     * Object constructor
     * 
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        Event::fire('framework.module.initialize.before', array($this->moduleName));

        $this->addModuleEvents();
        
        Event::add('framework.router.construct.after', function($router){
            $router->addRedirects($this->getRedirects());
            $router->addRoutes($this->getRoutes());
        });

        Event::fire('framework.module.initialize.after', array($this->moduleName));
    }

    /**
     * Create module-specific events
     */
    private function addModuleEvents()
    {
        if ($this->getObserverClass() !== null) {
            $obsClass = $this->getObserverClass();
            $moduleObserver = new $obsClass();

            if ($moduleObserver instanceof SubscriberInterface) {
                $events = $moduleObserver->getSubscribedEvents();

                foreach ($events as $name => $callback) {
                    if(is_array($callback)){
                        foreach ($callback as $call){
                            Event::add($name, array($moduleObserver, $call));
                        }
                    }else{
                        Event::add($name, array($moduleObserver, $callback));
                    }
                }
            }
        }
    }

    /**
     * 
     * @param type $method
     * @return \THCFrame\Module\Exception\Implementation
     */
    protected function _getImplementationException($method)
    {
        return new Exception\Implementation(sprintf('%s method not implemented', $method));
    }

    /**
     * Get module-specific routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Get module-specific redirects
     * 
     * @return array
     */
    public function getRedirects()
    {
        return $this->_redirects;
    }

}
