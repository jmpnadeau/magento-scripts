<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE":
 * <jm@jmpnadeau.ca> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return.
 * ----------------------------------------------------------------------------
 */


require_once 'Config.php';


class Dir
{
    
    protected $_dir = null;
    
    
    public function __construct($dir)
    {
        if (empty($dir))
        {
            throw new Exception("Invalid directory path supplied: '{$dir}'.");
        }
        
        
        $config   = Config::getInstance();
        $basePath = $config->getValue('magento', 'path');
        
        $this->_dir = realpath($basePath . $dir);
        
        if (!is_dir($this->_dir))
        {
            throw new Exception("No such directory: '{$this->_dir}'.");
        }
    }
    
    
    public function clean()
    {
        $contents = scandir($this->_dir);
        
        $filesCount = 0;
        
        foreach ($contents as $childNode)
        {
            if ($childNode != '.' && $childNode != '..')
            {
                $filePath = $this->_dir . '/' . $childNode;
                $success = unlink($filePath);
                
                if ($success === false)
                {
                    throw new Exception("Could not delete file '{$filePath}'.");
                }
                
                ++$filesCount;
            }
        }
        
        
        return $filesCount;
    }
    
}
