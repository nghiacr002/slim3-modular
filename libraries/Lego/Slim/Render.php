<?php
namespace Lego\Slim;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\InvalidMethodException;
use Interop\Container\ContainerInterface;
use Lego\Interfaces\ViewInterface;
use Slim\Exception\NotFoundException;
use Lego\Helper\GetDataFromContainerTrait;
class Render
{
    use GetDataFromContainerTrait;
    protected $container; 
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * Modular middleware
     * Dispatching router and redirect request into correct module & action
     * In case un-registered route, system will try to parse and find
     * return not found in case nothing found
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $result = $next($request, $response);
        $uri = $request->getUri()->getPath();
        $router = $this->container->get('router');
        if($this->container->has('view'))
        {
            $view = $this->container->get('view');
        }
        else
        {
            $view = null;
        }
        if($view instanceof ViewInterface)
        {
            $mode = $this->getSetting('forceResponseEndcoding');
            $result = $view->getResponse($mode);
        }
        return $result;
    }
}
    
?>