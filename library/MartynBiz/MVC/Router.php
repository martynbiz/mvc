<?php
/**
* Router class
* This is a seperate class which allows for unit testing specific to this class only
* 
*/

namespace MartynBiz\MVC;

/**
* Router
*/
class Router
{
    protected $router;
    protected $config;
    
    /** 
    * Construct the object and initiate the route defining
    * 
    * @param $config array Our routes config
    */
    public function __construct($config=array())
    {
        $this->init($config);
    }
    
    /** 
    * Allows routes to be set after initialisation if prefered
    * 
    * @param $config array Our routes config
    */
    public function init($config)
    {
        $this->config = $config;
    }
    
    /** 
    * Attempt to match a url and method
    * 
    * @param $config array Our routes config
    * @param $url string Used when we are grouping urls
    * @param $config array (leave blank) Config node, used for recurssive digging
    * @param $pattern string (leave blank) As we build the pattern when we dig through the route configs
    * @param $result array | false (leave blank) Our result value, set by reference when digging
    *
    * @return array | false Array of params if match, false if not a match
    */
    public function getRoute($url, $method='GET', $config=null, $pattern='', &$result=false)
    {
        // set method to upper
        $method = strtoupper($method);
        
        // first time call will require us to set config
        if (is_null($config))
            $config = $this->config;
        
        foreach($config as $key => $value) {
            if($key[0] === '/') {
                $config = $value;
                $this->getRoute($url, $method, $config, $pattern.$key, $result);
            } elseif(strtoupper($key) === $method) {
                // compare url
                
                $params = $this->compare($url, $pattern);
                
                if(is_array($params)) {
                    // match found, build $result
                    $result = $value;
                    $result['params'] = $params;
                    return $result;
                }
            }
        }
        
        return $result;
    }
    
    /**
    * Compare a url string with a route pattern
    *
    * @param $url string The url to compare
    * @param $pattern string The route pattern (preg, without the delimeters e.g. /accounts/(\d+))
    *
    * @return array | false Array of params if match, false if not a match
    */
    public function compare($url, $pattern)
    {
        // set case
        $url = strtolower($url);
        
        // tidy url
        $url = trim($url);
        if(strlen($url) > 1)
            $url = rtrim($url, '/');
        
        // tidy pattern
        $pattern = trim($pattern);
        if(strlen($pattern) > 1)
            $pattern = rtrim($pattern, '/');
        
        // add regular expression delimeters
        $pattern = '#^' . $pattern . '$#i';
        
        // perform regular expression, put result into $params
        preg_match_all($pattern, $url, $params, PREG_SET_ORDER);
        
        // if this array is empty we didn't have a match
        if(empty($params))
            return false;
        
        // we have a match!
        
        $params = $params[0];
        
        // we don't need the first element which is just the matching string
        $params = array_slice($params, 1);
        
        // if params, return the array, otherwise return an empty array
        return $params;
    }
    
    
}