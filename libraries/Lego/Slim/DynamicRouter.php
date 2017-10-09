<?php
namespace Lego\Slim;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\CallableResolverAwareTrait;
use Slim\MiddlewareAwareTrait;
use Slim\Exception\NotFoundException;
use Interop\Container\ContainerInterface;
use FastRoute;
class DynamicRouter
{
    use CallableResolverAwareTrait;
    use MiddlewareAwareTrait;
    protected $container;
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * Dynamic middleware. Auto detect & process Controller for request
     * redirect/rewrite all URLs that end in a / to the non-trailing / equivalent
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $route = $request->getAttribute('route');
        if(!$route)
        {
            $request = $this->modifyRequest($request);
        }
        return $next($request,$response); 
    }
    private function modifyRequest(RequestInterface $request)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $parts = explode("/",$path);
        
        if(count($parts) <= 0 || count($parts) > 3 || trim($path) == "/")
        {
            return $request;
        }
        $router = $this->container->get('router');
        $pattern = "/".$path;
        $routes = $router->getRoutes();
        foreach($routes as $name => $route)
        {
            if($route->getPattern() == $pattern)
            {
                return $request;
            }
        }
        $module = ucfirst($parts[0]);
        $controller = ((isset($parts[2]) && !empty($parts[2])) ? ucfirst($parts[1]) : "Index"). "Controller";
        $action = (isset($parts[2]) && !empty($parts[2])) ? ucfirst($parts[2]) : ucfirst($parts[1]);
        
        $classController = 'LegoAPI\\Modules\\'.$module.'\\Controller\\'. $controller;
        $callable = $classController.":".$action;
        try
        {
            $callable = $this->resolveCallable($callable);
        }
        catch(\RuntimeException $ex)
        {
            return $request;
        }
        
        $name = $module.'_'.$controller."_".$action;
        $route = $this->container->get('router')->map(['*'],$pattern,$callable);
        $route->setName($name);
        $request = $request->withAttribute('route', $route);
        //$routeInfo = $request->getAttribute('routeInfo');
        //$routeInfo[0] = FastRoute\Dispatcher::FOUND;
        $routeInfo = null;
        $router->setDispatcher(null);
        $router->resetCacheFile();
        $routerConfigs = $this->container['cache']->get('routerConfigs');
        if(count($routerConfigs))
        {
            $routerConfigs[$name] = array(
                'uri' => $pattern,
                'controller' => $classController,
                'action' => $action,
                'method' => ['*']
            );
            $this->container['cache']->save('routerConfigs',$routerConfigs);
        }
        $request = $request->withAttribute('routeInfo', $routeInfo);
        return $request;
    }
}
    
?>