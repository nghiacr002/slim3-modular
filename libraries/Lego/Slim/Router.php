<?php 
namespace Lego\Slim; 

use FastRoute\Dispatcher;
use Lego\Exception\DuplicateRouterException;
use Lego\Helper\GetDataFromContainerTrait;

class Router extends \Slim\Router
{
    use GetDataFromContainerTrait;
    /**
     * @param \FastRoute\Dispatcher $dispatcher.
     * In case dispatcher is null, router need to be reload routes and dispatchers
     */
    public function setDispatcher(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }
    /**
     * Reset cache file routers
     */
    public function resetCacheFile()
    {
        if(file_exists($this->cacheFile))
        {
            @unlink($this->cacheFile);
        }
        //$this->cacheFile = false;
    }
    
}