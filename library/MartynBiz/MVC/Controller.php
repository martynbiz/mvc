<?php

namespace MartynBiz\MVC;

/**
* Controller
*/
abstract class Controller
{
    /**
    * App instance so we can access to the view (e.g. set layout)
    */ 
    protected $app;
    
    /**
    * This is used by the controller to compare security tokens when POST, PUT, or DELETE is used
    */
    protected $_securityToken;
    
    /**
    * Param filter is used to protected against mass-assignment attacks. Is set in the controller
    */
    protected $paramFilter;
    
    function __construct(\MartynBiz\MVC\Application $app=null)
    {
        if($app instanceof \MartynBiz\MVC\Application)
            $this->init($app);
        
        // set the security token based on what's in session
        $this->_securityToken = $_SESSION['_SECURITYTOKEN'];
    }
    
    /**
    * Init function can be called after instantiation which works well for services
    * Allows us to set the app property once it's available
    */
    function init(\MartynBiz\MVC\Application $app)
    {
        $this->app = $app;
        
        // set the view layout based on the controllers own settings
        $view = $this->app->service('View');
        
        if(! $view instanceof \MartynBiz\MVC\View) {
            throw new \Exception('View service not instance of View');
        }
        
        // set the layout of this controller
        $view->setLayout( $this->layout );
        
        // protect against csrf attacks, set token here for the view
        // this method means that checking for the security token is
        // done every non-GET request, so it must be present
        if ($this->isPost() or $this->isPut() or $this->isDelete()) {
            $token = $this->getPost('_SECURITYTOKEN');
            $valid = $this->checkSecurityToken($token);
            if (! $valid)
                throw new \Exception('Invalid security token');
        }
    }
    
    
    
    
    // untested
    
    protected function returnArray($array)
    {
        if(! is_array($array))
            $array = array('data' => $array);
        
        $array['_SECURITYTOKEN'] = $this->getSecurityToken();
        
        return $array;
    }
    
    // just set it one time for each session
    protected function getSecurityToken()
    {
        if(is_null($this->_securityToken)) {
            $this->_securityToken = md5(time());
            $_SESSION['_SECURITYTOKEN'] = $this->_securityToken;
        }
        
        return $this->_securityToken;
    }
    
    protected function checkSecurityToken($token)
    {
        //echo "{$_SESSION['_SECURITYTOKEN']} == $token";
        
        return ($_SESSION['_SECURITYTOKEN'] == $token);
    }
    
    protected function filterParams($params)
    {
        if ($this->paramFilter) {
            $filter = array_fill_keys($this->paramFilter, true);
            return array_intersect_key($params, $filter);
        } else {
            return $params;
        }
    }
    
    public function isGet()
    {
        $method = $this->getMethod();
        
        return (strtoupper($method) == 'GET');
    }
    
    public function isPost()
    {
        $method = $this->getMethod();
        
        return (strtoupper($method) == 'POST');
    }
    
    /**
    * Return true if method is PUT. Browsers don't really support PUT so a hidden field
    * "method" is also passed
    * 
    * @return bool
    */
    public function isPut()
    {
        $method = $this->getMethod();
        
        return (strtoupper($method) == 'PUT');
    }
    
    /**
    * Return true if method is PUT. Browsers don't really support PUT so a hidden field
    * "method" is also passed
    * 
    * @return bool
    */
    public function isDelete()
    {
        $method = $this->getMethod();
        
        return (strtoupper($method) == 'DELETE');
    }
    
    /**
    * Return true if method is PUT. Browsers don't really support PUT so a hidden field
    * "method" is also passed
    * 
    * @return bool
    */
    public function getMethod()
    {
        return $this->app->environment('REQUEST_METHOD');
    }
    
    /**
    * Get the POST parameter(s)
    * 
    * @return bool
    */
    public function getPost($key=null)
    {
        if(is_null($key)) {
            return $_POST;
        } else {
            return (isset($_POST[$key])) ? $_POST[$key] : null;
        }
    }
    
    /**
    * Is this an AJAX request?
    * @return bool
    */
    public function isAjax()
    {
        return $this->app->isAjax();
    }
    
    // public function setStatus($status)
    // {
    //     header('status: ' . (int) $status);
    // }
    
    public function redirect($url, $status=302)
    {
        header('Location: ' . $url, true, (int) $status);
    }
}