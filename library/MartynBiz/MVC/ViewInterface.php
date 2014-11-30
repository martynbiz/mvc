<?php

namespace MartynBiz\MVC;

interface ViewInterface
{
    /**
     * Set the layout path. Allows us to change the layout in any controller
     *
     * @param $layout string Name of the layout file (path?)
     * @return void
     * @author Martyn Bissett
     **/
    public function setLayout($layout);
    
    /**
     * Require the template allows us to use PHP includes within in. This 
     * will return the template engine compiled result of the template and data
     * Used within the layout e.g. $this->yield($data)
     *
     * @param $data Data to compile template with
     * @return string The result of the template and data being compiled
     * @author Martyn Bissett
     **/
    public function embed($data=array());
    
    /**
     * Render the template within the layout
     *
     * @return void
     * @author Martyn Bissett
     **/
    public function render($data=array());
}