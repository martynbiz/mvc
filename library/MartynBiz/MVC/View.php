<?php

namespace MartynBiz\MVC;

class View implements ViewInterface
{
    /**
    * App instance so we can access the template/layout paths
    */ 
    protected $app;
    
    /**
    * Layout folder/file e.g. home/master.php. Different controllers can set different layouts
    */ 
    protected $layout;
    
    /**
    * Template folder/file e.g. home/index.php. Set in Application::run so we don't need to pass it to the layout
    */ 
    protected $template;
    
    /**
    * CSRF security token
    */ 
    protected $securityToken;
    
    /**
     * Constructor.
     *
     * @param $layout string Name of the layout file (path?)
     *
     * @param $app MartynBiz\MVC\Application App instance so we can access config and services
     * @return void
     * @author Martyn Bissett
     **/
    public function init($app)
    {
        $this->app = $app;
    }
    
    /**
     * Set the layout path. Allows us to change the layout in any controller
     *
     * @param $layout string Name of the layout file (path?)
     * @return void
     * @author Martyn Bissett
     **/
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    /**
     * Set the template path. 
     *
     * @param $template string Name of the template folder/file
     * @return void
     * @author Martyn Bissett
     **/
    public function setTemplate($template)
    {
        $this->template = $template;
    }
    
    /**
     * Get full template path e.g. /var/www/...
     *
     * @param $template string File/folder of template e.g. home/index.php
     * @return void
     * @author Martyn Bissett
     **/
    public function getTemplatePath()
    {
        $templatesDir = $this->app->config('templatesDir');
        
        // return full template path
        return $templatesDir . '/' . $this->template;
    }
    
    /**
     * Get full layout path e.g. /var/www/...
     *
     * @param $layout string File/folder of template e.g. home/index.php
     * @return void
     * @author Martyn Bissett
     **/
    public function getLayoutPath()
    {
        $layoutsDir = $this->app->config('layoutsDir');
        
        // return full template path
        return $layoutsDir . '/' . $this->layout;
    }
    
    /**
    * Is this an AJAX request?
    * @return bool
    */
    public function isAjax()
    {
        return $this->app->isAjax();
    }
    
    /**
    * Set the security token (from the controller)
    * @param $token string Token to set
    */
    public function setSecurityToken($token)
    {
        $this->securityToken = $token;
    }
    
    /**
     * Require the template allows us to use PHP includes within in. This 
     * will return the template engine compiled result of the template and data
     * Used within the layout e.g. $this->yield($data)
     *
     * @param $data Data to compile template with
     * @return string The result of the template and data being compiled
     * @author Martyn Bissett
     **/
    public function embed($data=array())
    {
        // set full template path
        $templatePath = $this->getTemplatePath($this->template);
        
        // get the template. This method allows us to use PHP code (e.g. include) 
        // within the template files. file_get_contents wouldn't allow that.
        ob_start();
        require $templatePath;
        return ob_get_clean();
    }
    
    /**
     * Render the template within the layout
     *
     * @return void
     * @author Martyn Bissett
     **/
    public function render($data=array())
    {
        $templatePath = $this->getTemplatePath();
        if (!is_file($templatePath)) {
            throw new \RuntimeException('View cannot render template ' . $this->template . ' because the file does not exist');
        }
        
        // check if layout has been set. if so, render together
        if ($this->layout) {
            
            //
            $layoutPath = $this->getLayoutPath();
            if (!is_file($layoutPath)) {
                throw new \RuntimeException('View cannot render layout ' . $this->layout . ' because the file does not exist');
            }
            
            // 
            ob_start();
            require $layoutPath;
            return ob_get_clean();
        } else {
            
            // no layout, just embed the template
            return $this->embed($data);
        }
    }
}