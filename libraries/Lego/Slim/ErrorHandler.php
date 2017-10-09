<?php
namespace Lego\Slim;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Handlers\AbstractHandler;
use Interop\Container\ContainerInterface;
use Lego\Interfaces\ViewInterface;
use Lego\Modular\View;
use Lego\Helper\General;
use Lego\Helper\GetDataFromContainerTrait;
class ErrorHandler extends  AbstractHandler
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
    public function __invoke(RequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        $mode = $this->getSetting('forceResponseEndcoding');
        $view = new View();
        if($mode == null)
        {
            $contentType = $this->determineContentType($request);
            $mode = $view->determineResponseMode($contentType);
        }
        $trackCode = General::generateTrackCode();
        $data = array(
            'ErrorCode' => $exception->getCode(),
            'ErrorMessage' => $exception->getMessage(),
        );
        if($this->isRequireLog($exception))
        {
            //Content for logger
            $message = "[".$this->container['appRuntimeInfo']['instanceId']."][".$trackCode."]"."[" . $exception->getCode() . "] " . $exception->getMessage() . "\n";
            $this->container['logger']->addInfo($message,['trace' => $exception->getTraceAsString()]);
            $data['TrackCode'] = $trackCode;
        }
        $view->setResponse($response);
        $view->setHeaderOptions($data);
        $view->set($data);
        return $view->getResponse($mode);
    }
    
    public function isRequireLog(\Exception $exception)
    {
        $settings = $this->getSetting('logger');
        $classes = isset($settings['errorTrack']) ? $settings['errorTrack'] : array(); 
        if(count($classes))
        {
            foreach($classes as $className)
            {
                if($exception instanceof $className)
                {
                    return true;
                }
            }
        }
        return false;
    }
}
    
?>