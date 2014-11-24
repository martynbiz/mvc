<?php

/*
Still to test...
- condtions etc is it a number? p:id toka
*/

use MartynBiz\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testClassInstantiates()
    {
        $config = array();
        
        $router = new Router($config);
        
        $this->assertTrue($router instanceof Router);
    }
    
    /**
    * @depends testClassInstantiates
    * @dataProvider urlMethodProvider
    */
    public function testGetRouteMethod($url, $method, $expected)
    {
        // initiate router
        
        $config = array(
            '/' => array(
                'GET' => array(
                    'controller' => 'home',
                    'action' => 'index',
                ),
            ),
            '/about' => array(
                'GET' => array(
                    'controller' => 'home',
                    'action' => 'about',
                ),
            ),
            '/hello' => array(
                '/' => array(
                    'get' => array( // lower case method
                        'controller' => 'hello',
                        'action' => 'index',
                    ),
                ),
                '/(\d+)' => array(
                    'GET' => array(
                        'controller' => 'hello',
                        'action' => 'show',
                    ),
                ),
                '/create' => array(
                    'GET' => array(
                        'controller' => 'hello',
                        'action' => 'create',
                    ),
                    'POST' => array(
                        'controller' => 'hello',
                        'action' => 'create',
                    ),
                ),
                '/(\d+)/edit' => array(
                    'GET' => array(
                        'controller' => 'hello',
                        'action' => 'edit',
                    ),
                    'PUT' => array(
                        'controller' => 'hello',
                        'action' => 'edit',
                    ),
                ),
                '/(\d+)/delete' => array(
                    'GET' => array(
                        'controller' => 'hello',
                        'action' => 'delete',
                    ),
                    'DELETE' => array(
                        'controller' => 'hello',
                        'action' => 'delete',
                    ),
                ),
            )
        );
        
        $router = new Router($config);
        
        // set test criteria
        
        $route = $router->getRoute($url, $method);
        
        $this->assertEquals($expected['controller'], $route['controller']);
        $this->assertEquals($expected['action'], $route['action']);
        
        // 
    }
    
    public function urlProvider()
    {
        return array(
            // invalid
            array('/hello', '/goodbye', false),
            
            // // basic url
            array('/', '/', array()),
            array('/hello', '/hello', array()),
            
            // // single params
            array('/hello/1', '/hello/(\d+)', array(1)),
            
            // multiple params in route pattern
            array('/hello/1/comments/2', '/hello/(\d+)/comments/(\d+)', array(1,2)),
            array('/hello/1/comments/2/replies/3', '/hello/(\d+)/comments/(\d+)/replies/(\d+)', array(1,2,3)),
            
            // trailing characters
            array('/hello/', '/hello', array()), // slashes on right side of url
            array('/hello', '/hello/', array()), // slashes on right side of pattern
            array(' /hello ', '/hello', array()), // spaces in url
            array('/hello', ' /hello ', array()), // spaces in pattern
            
            // case
            array('/HELLO', ' /hello ', array()), // spaces in pattern
            array('/hello', ' /HELLO ', array()), // spaces in pattern
        );
    }
    
    public function urlMethodProvider()
    {
        return array(
            // valid crud
            array('/hello', 'GET', array('controller'=>'hello','action'=>'index','params'=>array() )),
            array('/hello/create', 'GET', array('controller'=>'hello','action'=>'create','params'=>array() )),
            array('/hello/1', 'GET', array('controller'=>'hello','action'=>'show','params'=>array(1) )),
            array('/hello/1/edit', 'GET', array('controller'=>'hello','action'=>'edit','params'=>array(1) )),
            array('/hello/1/edit', 'PUT', array('controller'=>'hello','action'=>'edit','params'=>array(1) )),
            array('/hello/1/delete', 'GET', array('controller'=>'hello','action'=>'delete','params'=>array(1) )),
            array('/hello/1/delete', 'DELETE', array('controller'=>'hello','action'=>'delete','params'=>array(1) )),
            
            // invalid urls
            array('/goodbye', 'GET', false ),
            array('/goodbye/create', 'GET', false ),
            array('/goodbye/1', 'GET', false ),
            
            // invalid methods
            array('/hellow', 'XXXX', false ),
            array('/hellow/create', 'XXXX', false ),
            array('/hellow/1', 'XXXX', false ),
            array('/hellow/1/edit', 'XXXX', false ),
            array('/hellow/1/edit', 'XXXX', false ),
            array('/hellow/1/delete', 'XXXX', false ),
            array('/hellow/1/delete', 'XXXX', false ),
            
            // special cases
            array('/hello', 'get', array('controller'=>'hello','action'=>'index','params'=>array() )),
            array('/HELLO', 'GET', array('controller'=>'hello','action'=>'index','params'=>array() )),
        );
    }
}