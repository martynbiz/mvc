<?php

namespace MartynBiz\MVC;

/**
* Controller
*/
class Controller
{
    /**
    * App instance so we can access to the view (e.g. set layout)
    */ 
    protected $app;
    
    protected $layout = 'master.phtml'; // default
    
    /**
    * Init function can be called after instantiation which works well for services
    */
    function init(\MartynBiz\MVC\Application $app)
    {
        // set the view layout based on the controllers own settings
        $view = $app->service('View');
        
        if(! $view instanceof \MartynBiz\MVC\View) {
            throw new \Exception('View service not instance of View');
        }
        
        $view->setLayout( $this->layout );
        
        $this->app = $app;
    }
    
    // public function isXhr()
    // {
    //     return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    // }
}