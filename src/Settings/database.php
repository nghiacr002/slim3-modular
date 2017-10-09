<?php
/*$database = array( 
    'host' => 'localhost',
    'name' => 'simpleorm',
    'user' => 'root',
    'pwd' => '123456',
    'port' => 3306,
    'prefix' => 'tbl_',
    'adapter' => 'mysqli',
    'charset' => 'utf8',
    'type' => 'mysql'
);*/
$database = array(
   'default' => array(
       'driver' => 'mysql',
       'host' => 'localhost',
       'database' => 'simpleorm',
       'username' => 'root',
       'password' => '123456',
       'charset'   => 'utf8',
       'collation' => 'utf8_unicode_ci',
       'prefix'    => 'tbl_',
   ),
);
return $database;
?>