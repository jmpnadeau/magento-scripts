<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE":
 * <jm@jmpnadeau.ca> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return.
 * ----------------------------------------------------------------------------
 */


class Config
{

    protected static $_instance = null;
    
    protected $_config = null;
    
    
    
    protected function __construct()
    {
        $this->_parseConfig();
        $this->_parseMagentoConfig();
    }
    
    
    protected function _parseConfig()
    {
        $this->_config = parse_ini_file('config.ini', true);
        
        if (!is_array($this->_config))
        {
            throw new Exception('Could not parse config file.');
        }
    }
    
    
    protected function _parseMagentoConfig()
    {
        $magentoPath = realpath($this->_config['magento']['path']);
        $configPath = realpath($magentoPath . $this->_config['magento']['config_path']);
        
        if (empty($magentoPath) || empty($configPath))
        {
            throw new Exception('Invalid Magento path supplied.');
        }
        
        if (!file_exists($magentoPath) || !file_exists($configPath))
        {
            throw new Exception('Could not load Magento config: no such file or directory.');
        }
        
        
        try
        {
            $xml = simplexml_load_file($configPath);
            
            $dbConfig = $xml->global->resources->default_setup->connection;
            
            $this->_config['database']['hostname'] = (string) $dbConfig->host;
            $this->_config['database']['username'] = (string) $dbConfig->username;
            $this->_config['database']['password'] = (string) $dbConfig->password;
            $this->_config['database']['database'] = (string) $dbConfig->dbname;
            $this->_config['database']['init']     = (string) $dbConfig->initStatements;
        }
        catch (Exception $e)
        {
            throw new Exception('Could not parse Magento config: ' . $e->getMessage());
        }
    }
    
    
    public static function getInstance()
    {
        if (!(self::$_instance instanceof Config))
        {
            self::$_instance = new Config();
        }
        
        return self::$_instance;
    }
    
    
    public function getValue($section, $option)
    {
        if (!array_key_exists($section, $this->_config))
        {
            throw new Exception("No such section '{$section}'.");
        }
        
        if (!array_key_exists($option, $this->_config[$section]))
        {
            throw new Exception("No such option '{$option}'.");
        }
        
        
        return $this->_config[$section][$option];
    }
    
}
