<?php

use MartynBiz\Controller;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    protected $appMock;
    
    public function setUp()
    {
        // little messy here but basically, when controller is constructed it
        // will ask app for the service View. It will then set Views layout
        // so all this needs to be mocked
        
        $viewMock = $this->getMockBuilder('MartynBiz\MVC\View')
            ->disableOriginalConstructor()
            ->getMock();
        
        $appMock = $this->getMockBuilder('MartynBiz\MVC\Application')
            ->disableOriginalConstructor()
            ->getMock();
        
        $appMock //->expects( $this->once() )
            ->method('service')
            ->with('View')
            ->will( $this->returnValue($viewMock) );
        
        $this->appMock = $appMock;
    }
    
    public function testClassInstantiates()
    {
        $controller = new HomeController($this->appMock);
        
        $this->assertTrue($controller instanceof HomeController);
    }
    
    /**
     * 
     */
    public function testGetMethodGetsPost()
    {
        $environment = array(
            'REQUEST_METHOD' => 'POST',
        );
        
        $appMock = $this->appMock;
        
        $appMock->expects( $this->once() )
            ->method('environment')
            ->with('REQUEST_METHOD')
            ->will( $this->returnValue('POST') );
        
        $controller = new HomeController($appMock);
        
        $this->assertEquals($environment['REQUEST_METHOD'], $controller->getMethod());
    }
    
    public function testIsPost()
    {
        $environment = array(
            'REQUEST_METHOD' => 'POST',
        );
        
        $appMock = $this->appMock;
        
        $appMock->expects( $this->once() )
            ->method('environment')
            ->with('REQUEST_METHOD')
            ->will( $this->returnValue('POST') );
        
        $controller = new HomeController($appMock);
        
        $this->assertTrue($controller->isPost());
    }
    
    /**
     * 
     */
    public function testIsAjax()
    {
        $environment = array(
            'REQUEST_METHOD' => 'POST'
            // 'POST' => array( // this will outright replace POST
            //     '_METHOD' => 'PUT'
            // ),
        );
        
        $appMock = $this->appMock;
        
        $appMock->expects( $this->once() )
            ->method('environment')
            ->with('REQUEST_METHOD')
            ->will( $this->returnValue('POST') );
        
        $appMock->expects( $this->once() )
            ->method('service')
            ->with('View')
            ->will( $this->returnValue('POST') );
        
        $controller = new HomeController($appMock);
        
        $this->assertEquals($environment['REQUEST_METHOD'], $controller->getMethod());
    }
}