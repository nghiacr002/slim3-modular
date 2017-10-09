<?php

require_once 'Settings/constants.php';

require_once APP_LIB_PATH.'/vendor/autoload.php';
//require_once APP_LIB_PATH.'/SimpleORM/autoload.php';
require_once APP_LIB_PATH.'/Lego/autoload.php';
require_once APP_SOURCE_PATH. 'autoload.php';

if(!function_exists('d'))
{
    
    function d($info, $isDumping = false)
    {
        $isCLI = (PHP_SAPI == 'cli');
        (!$isCLI ? print '<pre style="text-align:left; padding-left:15px;">' : false);
        ($isDumping ? var_dump($info) : print_r($info));
        (!$isCLI ? print '</pre>' : false);
    }
}
?>