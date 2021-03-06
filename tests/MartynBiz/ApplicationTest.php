<?php

use MartynBiz\MVC\Application;

/**
* AppTest
*/
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    protected $viewMock;
    
    function setUp()
    {
        $this->viewMock = $this->getMockBuilder('MartynBiz\MVC\View')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    public function testClassInstantiates()
    {
        $app = new MartynBiz\MVC\Application();
        
        $this->assertTrue($app instanceof Application);
    }
    
    public function testInstanceOfViewIsCreatedWhenNotGiven()
    {
        $app = new Application();
        
        $view = $app->service('View');
        
        $this->assertTrue($view instanceof \MartynBiz\MVC\View);
    }
    
    function testConfigMethodGetsVariable()
    {
        $config = array(
            'test' => true,
        );
        
        $app = new MartynBiz\MVC\Application($config);
        
        $this->assertEquals(true, $app->config('test'));
    }
    
    // function testConfigMethodGetsAllVariablesWhenNoParametersPassed()
    // {
    //     $app = new MartynBiz\MVC\Application(array(
    //         'test' => true,
    //     ));
        
    //     $config = $app->config();
        
    //     $this->assertTrue( is_array($config) );
    //     $this->assertEquals( $config['test'], true );
    // }
    
    function testConfigMethodReturnsNullWhenInvalidNameIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $config = $app->config('idontexist');
        
        $this->assertTrue( is_null($config) );
    }
    
    function testConfigMethodSetsVariableWhenNameAndConfigIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $app->config('test', true);
        
        $this->assertEquals(true, $app->config('test'));
    }
    
    function testConfigMethodSetsMultipleConfigsWhenArrayIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $app->config(array(
            'test' => true,
        ));
        
        $this->assertEquals(true, $app->config('test'));
    }
    
    function testServiceMethodGetsVariableWhenNameIsPassed()
    {
        $testService = new stdClass();
        
        $config = array(
            'services' => array(
                'TestService' => $testService,
            ),
        );
        
        $app = new MartynBiz\MVC\Application($config);
        
        $this->assertEquals($testService, $app->service('TestService'));
    }
    
    function testServiceMethodGetsVariableWhenNameAndServiceIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $testService = new stdClass();
        
        $app->service( 'TestService', $testService );
        
        $this->assertEquals($testService, $app->service('TestService'));
    }
    
    function testServiceMethodSetsMultipleWhenArrayIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $testService = new stdClass();
        
        $app->service(array(
            'TestService' => $testService,
        ));
        
        $this->assertEquals($testService, $app->service('TestService'));
    }
    
    function testServiceMethodReturnsNullWhenInvalidNameIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $service = $app->service('idontexist');
        
        $this->assertTrue( is_null($service) );
    }
    
    
    
    
    function testEnvironmentMethodReturnsNullWhenInvalidNameIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $environment = $app->environment('idontexist');
        
        $this->assertTrue( is_null($environment) );
    }
    
    function testEnvironmentMethodSetsVariableWhenNameAndEnvironmentIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $app->environment('test', true);
        
        $this->assertEquals(true, $app->environment('test'));
    }
    
    function testEnvironmentMethodSetsMultipleEnvironmentsWhenArrayIsPassed()
    {
        $app = new MartynBiz\MVC\Application();
        
        $app->environment(array(
            'test' => true,
        ));
        
        $this->assertEquals(true, $app->environment('test'));
    }
    
    /**
     * @expectedException Exception
     */
    public function testRunThrowsExceptionWhenRouteIsMissing()
    {
        $config = array(
            'routes' => array(
                '/' => array(
                    'GET' => array(
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
        );
        
        $app = new Application($config);
        
        $app->run(array(
            'REQUEST_URI' => '/goodbye',
            'REQUEST_METHOD' => 'GET',
        ));
    }
    
    /**
     * This test requires routes to be working as we need to pass through that stage to
     * get to this point.
     * 
     * @expectedException Exception
     */
    public function testRunThrowsExceptionWhenControllerActionIsMissing()
    {
        $config = array(
            // we must set a valid route to pass to the next stage
            'routes' => array(
                '/home' => array(
                    'GET' => array(
                        'controller' => 'home',
                        'action' => 'idontexist',
                    ),
                ),
            ),
            'services' => array(
                'controllers.home' => new HomeController(), // defined in bootstrap
            )
        );
        
        $app = new Application($config);
        
        $app->run(array(
            'REQUEST_URI' => '/hello',
            'REQUEST_METHOD' => 'GET',
        ));
    }
    
    /**
     * @expectedException Exception
     */
    public function testRunThrowsExceptionWhenViewIsNotCorrectInterface()
    {
        $config = array(
            // we must set a valid route to pass to the next stage
            'routes' => array(
                '/home' => array(
                    'GET' => array(
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
            'services' => array(
                'controllers.home' => new HomeController(), // defined in bootstrap
                'View' => new stdClass(), // not instanceof ViewInterface
            )
        );
        
        $app = new Application($config);
        
        $app->run(array(
            'REQUEST_URI' => '/home',
            'REQUEST_METHOD' => 'GET',
        ));
    }
    
    /**
     * @depends testRunThrowsExceptionWhenRouteIsMissing
     * @depends testRunThrowsExceptionWhenControllerActionIsMissing
     * @depends testRunThrowsExceptionWhenViewIsNotCorrectInterface
     */
    public function testRunCallsRenderMethodOfView()
    {
        $viewMock = $this->viewMock;
        
        $viewMock->expects( $this->once() )
            ->method('init');
        
        $viewMock->expects( $this->once() )
            ->method('render');
        
        $config = array(
            // we must set a valid route to pass to the next stage
            'routes' => array(
                '/home' => array(
                    'GET' => array(
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
            'services' => array(
                'controllers.home' => new HomeController(), // defined in bootstrap
                'View' => $viewMock,
            )
        );
        
        $app = new Application($config);
        
        $app->run(array(
            'REQUEST_URI' => '/home',
            'REQUEST_METHOD' => 'GET',
        ));
    }
    
    
    
    
    
    function testEnvironmentMethodReturnsPut()
    {
        $environment = array(
            'REQUEST_METHOD' => 'POST',
            'POST' => array(
                '_METHOD' => 'PUT'
            ),
        );
        
        $app = new MartynBiz\MVC\Application(null, $environment);
        
        $this->assertEquals('PUT', $app->environment('REQUEST_METHOD'));
    }
    
    
    
    
    
    
    
    
    // testRenderIsNotCalledWhenControllerReturnsFalse
    // testRouteMatchedForRequestUriWhenQueryStringPassed e.g. /accounts?start=1
    // testRunThrowsExceptionWhenControllerActionIsMissingFromRoute()
    // testRunThrowsExceptionWhenControllerActionIsMissingFromController()
    // testRouteMatchedForRequestUriWhenQueryStringPassed e.g. /accounts?start=1
    
}