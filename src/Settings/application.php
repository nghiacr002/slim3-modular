<?php
use Lego\Exception\UnauthorizedException;
use Lego\Exception\DuplicateRouterException;
use Lego\Exception\IncompatibleRunningVersion;
use Lego\Exception\FatalErrorException;

$configurations = array(
    'settings' => array(
        'appVersion' => '1.0.0',
        'releaseDate' => '20/08/2017',
        'displayErrorDetails' => true, // output error detail when running
        'determineRouteBeforeAppMiddleware' => true,
        'versionControl' => array(
            'enable' => true, 
            'headerName' => 'version'
        ),
        'routerCacheFile' => APP_ASSET_PATH .'routers.php',
        'modular' => array(
            'basePath' => APP_SOURCE_PATH . "Modules" . DS,
            'allowToOverWriteRouter' => true,
        ),
        'router' => array(
            'cachePath' => APP_PUBLIC_PATH. "Cache" . DS,
            'enableCachingRouter' => true,
        ),
        'authenticate' => array(
            'method' => 'JWT', // or Basic
            'jwt' => array(
                'key' => '1234567889isoiadyuasynnn#!@!#*(DDDD',
                'algorithm' => 'HS256',
                'headerName' => 'X-Token', 
                'token' => array(
                    'iss' => "API LEGO",
                    'aud' => "*",
                    'iat' => null,
                    'nbf' => null
                ),
                'leeway' => 30 //second
            ),
            'basic' => array(
                'username' => '',
                'password' => '',
            )
        ),
        'forceResponseEndcoding' => 'JSON',
        'logger' => array(
            'path' => APP_LOG_PATH.'main.log',
            'enable' => true,
            'errorTrack' => array(
                //list of error that will be recorded
               UnauthorizedException::class,
               DuplicateRouterException::class,
               IncompatibleRunningVersion::class,
               FatalErrorException::class
            )
        ),
        'mode' => 'development',
        'security' => array(
            'secretKey' => '1234567890-=pounasd*#^!%%!#_()AQ*O&D(*AD888@@@'
        )
    )
);
$cache = require 'cache.php';
$dbconfig = require 'database.php';
$configurations['settings']['database'] = $dbconfig;
$configurations['settings']['cache'] = $cache;
return $configurations;
?>