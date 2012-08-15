<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE":
 * <jm@jmpnadeau.ca> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return.
 * ----------------------------------------------------------------------------
 */


include_once 'Config.php';


abstract class Singleton
{

    protected static $_instance = null;
    
    
    
    protected function __construct()
    {
        // Do nothing.
    }
    
    
    public static function getInstance()
    {
        $class = get_called_class();
        if (!(self::$_instance instanceof $class))
        {
            self::$_instance = new $class();
        }
        
        
        return self::$_instance;
    }
    
}
