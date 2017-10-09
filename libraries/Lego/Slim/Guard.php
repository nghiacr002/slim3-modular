<?php
namespace Lego\Slim;
use Interop\Container\ContainerInterface;
use Lego\Helper\GetDataFromContainerTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\CallableResolverAwareTrait;
/**
 * Setup basic GUARD with CORS
 * @author nghia
 *
 */
class Guard
{
    use CallableResolverAwareTrait;
    use GetDataFromContainerTrait;
    protected $container;
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * Guard middleware
     * only allow some valid header
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = $next($request, $response);
        $settings = $this->getSetting('authenticate');
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin,'.$settings['config']['headerName'])
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
    
?>