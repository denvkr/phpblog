<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpBlog;

/**
 * Description of autoloader
 *
 * @author dkrasavin
 */
class autoloader {
protected $classesMap = array();
    
    public function registerClass($className, $absolutePath)
    {
        if (file_exists($absolutePath)) {
            $this->classesMap[$className] = $absolutePath;
            return true;
        }
        
        return false;
    }
    
    public function autoload($class)
    {
        if (!empty($this->classesMap[$class])) {
            require_once $this->classesMap[$class];
            return true;
        }
        
        return false;
    }
}
