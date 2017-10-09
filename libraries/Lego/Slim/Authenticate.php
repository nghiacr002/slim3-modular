<?php
namespace Lego\Slim;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\CallableResolverAwareTrait;
use Interop\Container\ContainerInterface;
use Lego\Interfaces\AuthenticateInterface;
use Lego\Exception\UnAuthenticatedException;
use Lego\Helper\GetDataFromContainerTrait;
class Authenticate
{
    use CallableResolverAwareTrait;
    use GetDataFromContainerTrait;
    protected $container;
    protected $agent;
    public function __construct(ContainerInterface $container = null, AuthenticateInterface $authAgent = null)
    {
        $this->container = $container;
        if($authAgent == null)
        {
            $settings = $this->getSetting('authenticate');
            $callable = isset($settings['method']) ? $settings['method'] : "JWT"; //force to default using JWT in case no setting
            if(!class_exists($callable))
            {
                $callable = 'Lego\\Authenticate\\'. $callable;
            }
            try{
                $authAgent = new $callable;
            }
            catch(\Exception $ex)
            {
                throw new \RuntimeException("Generate agent of authenticate was failed: ".$ex->getMessage());
            }
            if(!($authAgent instanceof AuthenticateInterface))
            {
                throw new \RuntimeException("The authenticate agent must be implemented of ". AuthenticateInterface::class);
            }
        }
        $this->agent = $authAgent;
        $configs = isset($settings[$this->agent->getMethodName()]) ? $settings[$this->agent->getMethodName()] : array();
        $this->agent->setConfig($configs);
        $this->container['authenticate'] = $this->agent;
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
        if($route)
        {
            $name = $route->getName();
            $settings = $this->container->get('settings')['routerConfigs'];
            if(!empty($name) && isset($settings[$name]))
            {
                $isAuth = isset($settings[$name]['auth']) ? $settings[$name]['auth'] : false; 
                if($isAuth)
                {
                    //TODO
                    //Do Authorization here
                    try{
                        if(!$this->agent->verify($request))
                        {
                            throw new UnAuthenticatedException("UnAuthenticated request",HTTP_CODE_FORBIDDEN);
                        }
                        $request = $request->withHeader('verifiedInfo',$this->agent->getVerifiedInfo());
                    }
                    catch(\Exception $ex)
                    {
                        $this->container->get('logger')->addCritical($ex->getMessage(),['trace' => $ex->getTraceAsString()]);
                        throw new UnAuthenticatedException("UnAuthenticated request",HTTP_CODE_FORBIDDEN);
                    }
                    
                }
            }
        }
        $result = $next($request,$response);
        return $result;
    }
    /**
     * 
     * @return \Lego\Interfaces\AuthenticateInterface
     */
    public function getAgent()
    {
        return $this->agent;
    }
    
}
    
?>