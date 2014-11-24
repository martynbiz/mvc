<?php

use MartynBiz\View;

/**
* AppTest
*/
class ViewTest extends PHPUnit_Framework_TestCase
{
    
    protected $appMock;
    
    public function setUp()
    {
        $this->appMock = $this->getMockBuilder('MartynBiz\Application')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    public function testClassInstantiates()
    {
        $view = new MartynBiz\View($this->appMock);
        
        $this->assertTrue($view instanceof View);
    }
    
    // /**
    //  * @expectedException Exception
    //  */
    // public function testRenderThrowsExceptionWhenTemplateFileDoesntExist()
    // {
    //     // prepare app mock
    //     $appMock = $this->appMock;
    //     $appMock->expects( $this->once() )
    //         ->method('config')
    //         ->with('templatesDir')
    //         ->will( $this->returnValue('home/idontexist') );
        
    //     // prepare view
    //     $view = new View();
    //     $view->init($appMock);
        
    //     $templatePath = 'home/index.phtml';
        
    //     $view->render($templatePath);
    // }
    
    // /**
    //  * @expectedException Exception
    //  */
    // public function testRenderThrowsExceptionWhenLayoutFileDoesntExist()
    // {
    //     // prepare app mock
    //     $appMock = $this->appMock;
    //     $appMock->expects( $this->once() )
    //         ->method('config')
    //         ->with('layoutsDir')
    //         ->will( $this->returnValue('idontexist') );
        
    //     // prepare view
    //     $view = new View();
    //     $view->init($appMock);
        
    //     $templatePath = 'home/index.phtml';
        
    //     $view->render($templatePath);
    // }
    
}