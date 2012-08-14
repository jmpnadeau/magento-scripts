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
 * clean-tmp-files.php
 * 
 * Removes a set of temporary files to reduce disk usage.
 * 
 */


// Dependencies.
include 'classes/Dir.php';


$time_start = microtime(true);


try
{
    $dir = new Dir('/var/cache');
    $filesCount = $dir->clean();
    
    // Compute execution time.
    $time_end  = microtime(true);
    $time_exec = $time_end - $time_start;
    
    // Exit.
    $config = Config::getInstance();
    if ($config->getValue('general', 'quiet_mode') == '0')
    {
        echo "Successfully removed {$filesCount} files in {$time_exec} seconds." . PHP_EOL;
    }
    
    exit;
}
catch (Exception $e)
{
    die('A fatal error has occured: ' . $e->getMessage() . PHP_EOL);
}
