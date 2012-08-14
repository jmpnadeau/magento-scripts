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
 * clean-logs.php
 * 
 * Dump logs to file.
 * 
 */


include 'classes/Db.php';

$time_start = microtime(true);

try
{
    $db = Db::getInstance();
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
    
    // Tables to dump.
    $tables = array(
        'dataflow_batch_export',
        'dataflow_batch_import',
        'log_customer',
        'log_quote',
        'log_url',
        'log_url_info',
        'log_visitor',
        'log_visitor_info',
        'log_visitor_online',
        'report_event'
    );
    
    $config = Config::getInstance();
    $tablesPrefix = $config->getValue('database', 'tables_prefix');
    
    $storagePath = realpath($config->getValue('general', 'logs_storage_path'));
    if ($storagePath === false)
    {
        throw new Exception('Logs storage path does not exist.');
    }
    
    $entries = 0;
    foreach ($tables as $table)
    {
        // Open file.
        $handle = fopen("compress.zlib://{$storagePath}/{$table}.csv.gz", 'a');
        if (!is_resource($handle))
        {
            throw new Exception('Could not open log file for writing.');
        }
        
        // Find primary key.
        $primaryKey = null;
        $structure = $db->query("DESCRIBE `{$tablesPrefix}{$table}`")->fetchAll();
        foreach ($structure as $column)
        {
            if ($column['Key'] == 'PRI')
            {
                $primaryKey = $column['Field'];
            }
        }
        
        if (is_null($primaryKey))
        {
            throw new Exception('Could not find the table\'s primary key.');
        }
        
        // Read table.
        $ids = array();
        $result = $db->query("SELECT * FROM `{$tablesPrefix}{$table}` LIMIT 100000");
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            // Write line to file.
            $bool = fputcsv($handle, array_values($row));
            if (!$bool)
            {
                throw new Exception('Could not write log line to file.');
            }
            
            // Log dumped rows for removal.
            $ids[] = $row[$primaryKey];
        }
        
        $entries = $entries + count($ids);
        
        // Close file.
        fclose($handle);
        
        // Delete dumped rows.
        $ids = implode(',', $ids);
        $db->query("DELETE FROM `{$tablesPrefix}{$table}` WHERE `{$primaryKey}` IN ({$ids})");
    }
    
    // Compute execution time.
    $time_end  = microtime(true);
    $time_exec = $time_end - $time_start;

    // Exit.
    if ($config->getValue('general', 'quiet_mode') == '0')
    {
        echo "Successfully cleaned {$entries} entries in " . count($tables) . " tables in {$time_exec} seconds." . PHP_EOL;
    }

    exit;
    
}
catch (Exception $e)
{
    die('A fatal error has occured: ' . $e->getMessage() . PHP_EOL);
}
