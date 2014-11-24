<?php

namespace MartynBiz\MVC;

/**
* Having an App class acts as a registry and service locator
*/
class Application
{
    /**
    * this is the applciation's config settings
    */
    protected $config = array();
    
    /**
    * this is the applciation's services
    */
    protected $services = array();
    
    /**
    * this is the applciation's services
    */
    protected $environment = array();
    
    /**
    * this is the router for the app
    */
    protected $router;
    
    /**
    * Class constructor. Set config, services
    */
    public function __construct($config=array())
    {
        // initiate this here and set config
        $this->router = new Router();
        if (isset($config['routes'])) {
            $this->router->init($config['routes']);
        }
        
        // set services
        
        // set services if in config, discard when done
        if (isset($config['services'])) {
            $this->service($config['services']);
            unset($config['services']); // we don't need services 
        }
        
        // if view is not set, we will use the frameworks View class
        if (is_null( $this->service('View') )) {
            $defaultView = new View();
            $this->service('View', $defaultView);
        }
        
        // set config
        $this->config($config);
    }
    
    /**
    * Get or set config. Can store simple data type setting, and multiple setting with array
    *
    * @param mixed $config Either the value of a single config, an array containing many configs, or blank
    * @param mixed $value Value of the config to set, or blank
    * 
    * @return mixed Config value(s)
    */
    public function config($config=null, $value=null)
    {
        return $this->setResource( $this->config, $config, $value );
    }
    
    /**
    * Get or set service. Can store objects, functions (factory), strings (auto instantiate)
    *
    * @param mixed $config Either the value of a single config, an array containing many configs, or blank
    * @param mixed $value Value of the config to set, or blank
    * 
    * @return mixed Config value(s)
    */
    public function service($service, $value=null)
    {
        return $this->setResource( $this->services, $service, $value );
    }
    
    /**
    * Get or set environment. Intended to store strings only (althoug can store anything)
    *
    * @param mixed $config Either the value of a single config, an array containing many configs, or blank
    * @param mixed $value Value of the config to set, or blank
    * 
    * @return mixed Config value(s)
    */
    // public function environment($environment, $value=null)
    // {
    //     return $this->setResource( $this->environment, $environment, $value );
    // }
    
    /**
    * Config and services are essentially the same so we can use a common method for those
    * 
    * @param array $resouce Our internal resource property such as config or services
    * @param mixed $config Either the value of a single config, an array containing many configs, or blank
    * @param mixed $value Value of the config to set, or blank
    * 
    * @return mixed Config value(s)
    */
    protected function setResource(&$resource, $config, $value=null)
    {
        if (is_array($config)) { // multiple set
            
            // merge config with array
            $this->config = array_merge($this->config, $config);
            
        } elseif(! is_null($value)) { // value passed
            
            // set config
            $this->config[$config] = $value;
            
        } else { // value not passed, return config
            
            // return config
            if(isset($this->config[$config])) {
                $service = $this->config[$config];
                
                // we're do this here so that it's only executed when it's required
                if(is_callable($service)) {
                    $this->config[$config] = $service();
                }
                
                return $service;
            }
        }
        
        return null;
        
        // // return all
        // return $this->config;
    }
    
    /**
    * Used to initiate items that require configs to be in app (e.g. models for db configs)
    */
    public function bootstrap()
    {
        $bootstrapPath = $this->config('bootstrapPath');
        
        if (is_file($bootstrapPath)) {
            require $bootstrapPath;
        }
        
        return $this;
    }
    
    /**
    * Run the application. Fetch the route for the url from the router, dispatch the controller, render the view
    * 
    * @param $environment array Environment can be overridden when testing
    */
    public function run($environment=array())
    {
        $config = $this->config();
        
        // set the environment from $_SERVER, $_POST, $environment, ...
        $environment = array_merge($_SERVER, array(
            'POST' => $_POST,
        ), $environment);
        
        // load the environment into memory so that view and controller have access
        // $this->environment($environment);
        
        // get the route for this url
        $url = (isset($environment['PATH_INFO'])) ? $environment['PATH_INFO'] : $environment['REQUEST_URI'];
        $method = $environment['REQUEST_METHOD'];
        $route = $this->router->getRoute($url, $method);
        
        if(! $route)
            throw new \Exception('Route for "' . $url . '" not found.'); // ** test
        
        // ** test when controller or action not set
        
        // dispatch the route
        
        // controllers are require in services so we can test them in the application.
        // for example, if we want to see how the app respond when a controller's action
        // is missing we need to be able to set the controller in the test. However, it's
        // shit we can't set the app when we set the service in config so we'll use init()
        // for that purpose
        $controller = $this->service('controllers.' . $route['controller']);
        
        // **test
        // check if controller has been set
        if(! $controller)
            throw new \Exception('Controller not found in services.');
        
        $actionMethod = $route['action'] . 'Action';
        
        $controller->init($this);
        
        // ** test exception is thrown if action method missing
        if(! method_exists($controller, $actionMethod))
            throw new \Exception('Controller action method "' . $actionMethod . '" not found.');
        
        // result may be an array, in which case it will be passed to the default view. Or, it
        // will be an object with a render function
        $data = call_user_func_array(array($controller, $actionMethod), $route['params']);
        
        // if we don't get a array back from the above, set as an empty array anyway
        if(! is_array($data)) $data = array();
        
        // 
        $view = $this->service('View');
        
        //
        if(! $view instanceof \MartynBiz\MVC\View) {
            throw new \Exception('View service not instance of View');
        }
        
        // pass the view an instance of the app
        $view->init($this);
        $view->setTemplate( $route['controller'] . '/' . $route['action'] . '.php' );
        
        echo $view->render($data);
    }
    
}