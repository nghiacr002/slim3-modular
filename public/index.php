<?php
require_once __DIR__.'/../src/init.php';
$settings = require APP_SETTING_PATH. 'application.php';
if($settings['settings']['mode'] == "development")
{
    require_once APP_SOURCE_PATH.'Settings/development.php';
}
else
{
    require_once APP_SOURCE_PATH.'Settings/production.php';
}
$app = new Lego\Slim\App($settings);
$app->run(); 
?>