<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE":
 * <jm@jmpnadeau.ca> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return.
 * ----------------------------------------------------------------------------
 */

// There are compatibility functions for PHP version < 5.3

// get_called_class
if ( function_exists( 'get_called_class' ) )
{
    function get_called_class ()
    {
        $trace = debug_backtrace(); 
        $trace = $trace[0];
        
        if ( isset( $trace['object'] ) && $trace['object'] instanceof $trace['class'] )
        {
            return get_class( $trace['object'] );
        }
        
        
        return false;
    }
}  