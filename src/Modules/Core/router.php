<?php
use LegoAPI\Modules\Core\Controller\AuthController;
use LegoAPI\Modules\Core\Controller\IndexController;

$router = array(
    'core_hello' => array(
        'uri' => '/core/hello',
        'controller' => IndexController::class, 
        'action' => 'Hello',
        'method' => ['GET'],
        'auth' => true, 
        'versionCompatible' => array(
            'min' => 10, 
            'max' => null,
        ),
        'description' => "",
        'roles' => []
    ),
    'core_authenticate' => array(
        'uri' => '/authenticate',
        'controller' => AuthController::class,
        'action' => 'Authenticate',
        'method' => ['POST'],
    )
);
return $router;
?>