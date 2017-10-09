<?php
namespace Lego\Slim;
use Interop\Container\ContainerInterface;
use Lego\Helper\GetDataFromContainerTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\CallableResolverAwareTrait;
use Slim\Exception\InvalidMethodException;
use Slim\Exception\NotFoundException;
use Lego\Helper\General;
use Lego\Exception\IncompatibleRunningVersion;
class VersionControl
{
    use CallableResolverAwareTrait;
    use GetDataFromContainerTrait;
    protected $container;
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * TraillingSlash middleware
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
        //$settings = $this->container->get('settings');
        //$versionSettings = isset($settings['versionControl']) ? $settings['versionControl'] : array() ;
        $versionSettings = $this->getSetting('versionControl');
        if(!isset($versionSettings['enable'])  || $versionSettings['enable'] != true)
        {
            return $next($request, $response);
        }
        
        $route = $request->getAttribute('route');
        if(!$request->hasHeader($versionSettings['headerName']))
        {
            return $next($request, $response);
        }
        
        $version = $request->getHeader($versionSettings['headerName']); 
        if(is_array($version) && count($version) >=1)
        {
            $version = $version[0]; 
        }
        if(!empty($version))
        {
            if($route)
            {
                //valid version control
                $config = $this->getRouterConfig($route->getName());
                if(isset($config['versionCompatible']) && $config['versionCompatible'])
                {
                    $min = isset($config['versionCompatible']['min']) ? $config['versionCompatible']['min'] : null;
                    $max = isset($config['versionCompatible']['max']) ? $config['versionCompatible']['max'] : null;
                    if(!General::isCompatibleVersion($version,$min,$max))
                    {
                        throw new IncompatibleRunningVersion("API is not compatible with system");
                    }
                }
                $callable = $route->getCallable();
                if(is_string($callable))
                {
                    $parts = explode('\\',$callable);
                    array_splice($parts, 3,0,array('Version','V'.$version));
                    $callable = implode('\\', $parts);
                    try{
                        $callable = $this->resolveCallable($callable);
                    }
                    catch(\RuntimeException $ex)
                    {
                        throw new NotFoundException($request,$response);
                    }
                    $route->setCallable($callable);
                }
            }
            else
            {
                throw new NotFoundException($request,$response);
            }
        }
        
        return $next($request, $response);
    }
}
    
?>