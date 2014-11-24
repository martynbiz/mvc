<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname('../'));

// require composer autoloader for loading classes
require 'vendor/autoload.php';

// for testing and mocking controllers
class HomeController extends MartynBiz\Controller
{
    public function indexAction()
    {
        
    }
    
    public function showAction($id)
    {
        
    }
}