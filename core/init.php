<?php
session_start();

spl_autoload_register(function($classname){
    if (file_exists('classes/class.'.strtolower($classname).'.php')) {
        require_once('classes/class.'.strtolower($classname).'.php');
    } elseif (file_exists('../classes/class.'.strtolower($classname).'.php')) {
        require_once('../classes/class.'.strtolower($classname).'.php');
    }
});

            DEFINE ('DB_HOST', 'localhost');
            DEFINE ('DB_USER', 'highxjac_wp899');  
            DEFINE ('DB_PASSWORD', '3P-57.SpB0');
            DEFINE ('DB_NAME', 'highxjac_wp899');
     
        $conn = new MySQLi(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL');
        mysqli_set_charset( $conn, 'utf8');

?>