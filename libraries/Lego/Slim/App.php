<?php
namespace Lego\Slim;
use Lego\Exception\DuplicateRouterException;
use Slim\HttpCache\CacheProvider as HttpCacheProvider;
use Slim\App as SlimApp;
use Lego\Slim\ErrorHandler as LegoErrorHander;
use Monolog\ErrorHandler;
use Slim\Http\Response;
use Lego\Modular\View;
use Lego\Helper\General;
use Lego\Exception\FatalErrorException;
use Lego\Helper\GetDataFromContainerTrait;
use Lego\Database\Connector;
use Lego\Slim\Cache as LegoCacheManager;
use Lego\Slim\Router as LegoRouter;
use Slim\Container;
class App extends SlimApp
{
    private $container;
    private $instanceId;
    use GetDataFromContainerTrait;
    static $instance;
    public function __construct($container = [])
    {
        //register default services
        $container = $this->registerDefaultServices($container);
        parent::__construct($container);
        $container = $this->container = parent::getContainer();
        $settings = $this->container->get('settings');
        $options =  isset($settings['modular']) ? $settings['modular'] : array();
        if(!isset($options['basePath']))
        {
            throw new \Exception("required basePath for modular");
        }
        //register all default routers
        $this->fetchRouters();
        
        //force redirect "none slash" to "slash"
        $this->add(new TraillingSlash());
        
        //register Version Manager
        $this->add(new VersionControl($this->getContainer()));
        
        //register dynamic router parser 
        $this->add(new DynamicRouter($this->getContainer()));
        
        //register Authenticate method
        $this->add(new Authenticate($this->getContainer()));
        
        //register RenderEngine
        $this->add(new Render($this->getContainer()));
        
        //register Guard
        $this->add(new Guard($this->getContainer()));
        
        self::$instance = $this;
    }
    /**
     * Register default services for running
     */
    protected function registerDefaultServices($container)
    {
        if($container instanceof Container)
        {
            $settings = $container->get('settings');
        }
        else
        {
            $settings = isset($container['settings']) ? $container['settings'] : array();
        }
        $this->setInstanceId();
        $container['appRuntimeInfo'] = array(
            'instanceId' => $this->getInstanceId()
        );
        $container['router'] = function ($container) {
            $routerCacheFile = false;
            if (isset($container->get('settings')['routerCacheFile'])) {
                $routerCacheFile = $container->get('settings')['routerCacheFile'];
            }
            $router = (new LegoRouter())->setCacheFile($routerCacheFile);
            if (method_exists($router, 'setContainer')) {
                $router->setContainer($container);
            }
            
            return $router;
        };
        $container['request'] = function ($container) {
            return \Lego\Slim\Request::createFromEnvironment($container->get('environment'));
        };
        $container['errorHandler'] = function($c){
            return new LegoErrorHander($c);
        };
        $container['cache'] = function($c){
            $settings = $c->get('settings')['cache'];
            $cacheManager = new LegoCacheManager($settings);
            return $cacheManager;
        };
        
        $cacheHttpConfig = isset($settings['cache']['httpCache']) ? $settings['cache']['httpCache'] : null;
        //caching provide by Slim Cache
        if($cacheHttpConfig)
        {
            $container['httpCache'] = function () {
                return new HttpCacheProvider();
            };
            $this->add(new \Slim\HttpCache\Cache($cacheHttpConfig['type'], $cacheHttpConfig['age'],$cacheHttpConfig['mustRevalidate']));
        }
        
        if($settings['logger']['enable'] == true)
        {
            $logger = new \Monolog\Logger('LegoAPI');
            $file_handler = new \Monolog\Handler\StreamHandler($settings['logger']['path']);
            $formatter = new \Monolog\Formatter\LineFormatter();
            $formatter->includeStacktraces();
            $file_handler->setFormatter($formatter);
            $logger->pushHandler($file_handler);
            $container['logger'] = function($c) use($logger){
                return $logger;
            };
            //do not register handler for fatal error.
            //Using custom function to prevent application stop working.
            ErrorHandler::register($logger,array(),false,false);
        }
        register_shutdown_function(array($this, 'handleFatalError'));
        $dbConfigs = $settings['database'];
        $capsule = new Connector();
        foreach($dbConfigs as $sDbNameConnection => $aConfig)
        {
            $capsule->addConnection($aConfig,$sDbNameConnection);
        }
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $container['db'] = function($c) use($capsule){
            return $capsule;
        };
        return $container;
    }
    /**
     * Check if app is in development mode or not
     */
    public function isInDevelopmentMode()
    {
        return ($this->getSetting('mode') == 'development' );
    }
    /**
     * Get current running instance
     * @return \Lego\Slim\App
     */
    public static function getInstance()
    {
        return self::$instance;
    }
    /**
     * Handle Fatal Error
     */
    public function handleFatalError()
    {
        $settings = $this->container->get('settings');
        $mode = isset($settings['forceResponseEndcoding']) ? $settings['forceResponseEndcoding'] : null;
        $lastError = null;
        $exception = error_get_last();
        $trackCode = General::generateTrackCode();
        if($settings['logger']['enable'] == true)
        {
            $lastError = error_get_last();
            $arrayHanders = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
            if($lastError && in_array($lastError['type'], $arrayHanders, true))
            {
                $exception = new FatalErrorException();
                $exception->setFile($lastError['file']);     
                $exception->setLine($lastError['line']);     
                $exception->setInternalMessage($lastError['message']);     
                $message = "[".$this->container['appRuntimeInfo']['instanceId']."][".$trackCode."]"."[" . $exception->getCode() . "] " . $exception->getInternalMessage() . "\n";
                $this->container['logger']->addCritical($message,['trace' => $exception->getTraceAsString()]);
                $this->runFatalError($trackCode,$exception, $mode);
            }
        }
        
    }
    /**
     * Run in case Fatal Error
     * @param string $trackCode
     * @param \Exception $ex
     * @param string $mode
     * @return \Slim\Http\Response
     */
    public function runFatalError($trackCode, \Exception $ex = null, $mode = "")
    {
        $response = new Response();
        $view = new View();
        $view->setResponse($response);
        $code = "####"; 
        $message = "Unknown"; 
        if($ex)
        {
            $code = $ex->getCode(); 
            $message = $ex->getMessage();
        }
        $data = array(
            'ErrorCode' => $code,
            'ErrorMessage' => $message,
            'TrackCode' => $trackCode
        );
        $view->setHeaderOptions($data);
        $view->set($data);
        
        $response = $view->getResponse($mode);
        $this->respond($response);
        return $response;
    }
    /**
     * Register all routers which are defined in basePath/Module_Name/router.php;
     */
    public function fetchRouters()
    {
        $modularSetting = $this->getSetting('modular');
        if($this->isInDevelopmentMode())
        {
            //always reset router
            $this->container->get('router')->setDispatcher(null);
            $routerConfigs = null;
            $this->container->get('router')->resetCacheFile();
        }
        else
        {
            $routerConfigs = $this->container['cache']->get('routerConfigs');
        }
        if(!$routerConfigs)
        {
            $this->container->get('router')->resetCacheFile();
            $basePath = $modularSetting['basePath'];
            $items = @scandir($basePath);
            if(count($items) && is_array($items))
            {
                foreach($items as $key => $item)
                {
                    $path = $basePath. $item . DS;
                    $routerFile = $path.'router.php';
                    if(is_dir($path) && file_exists($routerFile))
                    {
                        $routerFileConfigs = require $routerFile;
                        if(is_array($routerFileConfigs) && count($routerFileConfigs))
                        {
                            foreach($routerFileConfigs as $name => $config)
                            {
                                if(isset($routerConfigs[$name]) && $modularSetting['allowToOverWriteRouter'] == false)
                                {
                                    throw new DuplicateRouterException("route ".$name . " was defined before");
                                }
                                $routerConfigs[$name] = $config;
                            }
                        }
                    }
                }
            }
            $this->container['cache']->save('routerConfigs',$routerConfigs,null);
        }
        if(is_array($routerConfigs) && count($routerConfigs))
        {
            foreach($routerConfigs as $name => $config)
            {
                $hander = $config['controller'];
                if(!isset($config['action']) || empty($config['action']))
                {
                    $config['action'] = "Index";
                }
                $hander.= ":". $config['action'];
                $route = $this->map($config['method'],$config['uri'],$hander);
                $route->setName($name);
            }
            $this->container->get('settings')['routerConfigs'] = $routerConfigs;
        }
    }
    /**
     * Return the instance Application runtime ID
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }
    /**
     * Set application instance ID
     * @param string $id
     * @return \Lego\Slim\App
     */
    public function setInstanceId($id = "")
    {
        if(empty($id))
        {
            $this->instanceId = "#". substr(md5(time().uniqid()),0,10);
        }
        return $this;
    }
    /**
     * Return the current App Version
     * @return NULL|string
     */
    public function getVersion()
    {
        return $this->getSetting('appVersion');
    }
    /**
     * Get Release Date 
     * @return NULL|string
     */
    public function getReleaseDate()
    {
        return $this->getSetting('releaseDate');
    }
}
    
?>