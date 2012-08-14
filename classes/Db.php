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
require_once 'Singleton.php';


class Db extends Singleton
{
    
    protected $_pdoInstance = null;
    
    
    
    protected function __construct()
    {
        $this->_connect();
    }
    
    
    protected function _connect()
    {
        $config = Config::getInstance();
        
        $hostname = $config->getValue('database', 'hostname');
        $username = $config->getValue('database', 'username');
        $password = $config->getValue('database', 'password');
        $database = $config->getValue('database', 'database');
        $init     = $config->getValue('database', 'init');
        
        try 
        {
            $this->_pdoInstance = new PDO(
                "mysql:host={$hostname};dbname={$database}", 
                $username, 
                $password, 
                array(PDO::MYSQL_ATTR_INIT_COMMAND => $init));
        }
        catch (Exception $e)
        {
            throw new Exception('Could not connect to the database: ' . $e->getMessage());
        }
    }
    
    
    public function __call($name, $arguments)
    {
        if (!method_exists($this->_pdoInstance, $name))
        {
            throw new Exception("Invalid method '{$name}'.");
        }
        
        return call_user_func_array(array($this->_pdoInstance, $name), $arguments);
    }
    
}
