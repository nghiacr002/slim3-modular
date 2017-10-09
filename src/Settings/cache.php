<?php 
$cache = array(
    'httpCache' => array(
        'type' => 'public',
        'age' => 84600,
        'mustRevalidate' => false
    ),
    'filesystem' => array(
        'path' => APP_CACHE_PATH
    ),
    'redis' => array(
        'default' => array(
            'host' => '127.0.0.1',
            'port' => 6379,
            'protocol' => 'tcp'
        )
    )
);
return $cache;