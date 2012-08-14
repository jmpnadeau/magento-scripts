<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE":
 * <jm@jmpnadeau.ca> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return.
 * ----------------------------------------------------------------------------
 */

/*
 * optimize-tables.php
 * 
 * Performs simple optimization techniques on the tables in database.
 * 
 */


// Dependencies.
include 'classes/Db.php';


$time_start = microtime(true);


try
{
    $db = Db::getInstance();
    
    // Get the list of tables in database.
    $tables = $db->query('SHOW TABLES');
    
    $tablesCount = 0;
    
    foreach ($tables as $table)
    {
        $table = $table[0];
        
        // Optimize tables.
        $db->query("ANALYZE TABLE {$table}");
        $db->query("OPTIMIZE TABLE {$table}");
        
        ++$tablesCount;
    }
    
    // Compute execution time.
    $time_end  = microtime(true);
    $time_exec = $time_end - $time_start;
    
    // Exit.
    $config = Config::getInstance();
    if ($config->getValue('general', 'quiet_mode') == '0')
    {
        echo "Successfully optimized {$tablesCount} tables in {$time_exec} seconds." . PHP_EOL;
    }
    
    exit;
}
catch (Exception $e)
{
    die('A fatal error has occured: ' . $e->getMessage() . PHP_EOL);
}
