<?php
namespace Lego\Slim;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
class TraillingSlash
{
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
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            
            if($request->getMethod() == 'GET') {
                return $response->withRedirect((string)$uri, HTTP_CODE_MOVED_PERMANENTLY);
            }
            else {
                return $next($request->withUri($uri), $response);
            }
        }
        return $next($request, $response);
    }
}
    
?>