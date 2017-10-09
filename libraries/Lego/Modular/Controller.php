<?php
namespace Lego\Modular; 
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Lego\Interfaces\ViewInterface;
use Lego\Helper\GetDataFromContainerTrait;
use Slim\Exception\MethodNotAllowedException;
//use Illuminate\Database\ConnectionInterface;
class Controller
{
    protected $container;
    protected $request; 
    protected $response; 
    protected $view;
    use GetDataFromContainerTrait;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->setRequest($container->get('request'));
        $this->setResponse($container->get('response'));
        $settings = $this->getSettings();
        $mode = isset($settings['render']) ? $settings['render'] : ViewInterface::MODE_RENDER_JSON;
        $this->view = new View();
        $this->view->setResponse($this->response);
        $this->view->setMode($mode);
        $this->container['view'] = $this->view;
    }
    
    /** 
     * Set Request instance of Psr\Http\Message\RequestInterface
     * @param RequestInterface $request
     * @return \Lego\Modular\Controller
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }
    /**
     * Set Response instance of Psr\Http\Message\ResponseInterface
     * @param ResponseInterface $response
     * @return \Lego\Modular\Controller
     */
    public function setResponse(Response $response)
    {
        $this->response = $response; 
        return $this;
    }
    /**
     * Set Render View Engine
     * @param ViewInterface $view
     * @return \Lego\Modular\Controller
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
        return $this;
    }
    /**
     * Get Authenticate Agent
     * @return \Lego\Interfaces\AuthenticateInterface
     */
    public function getAuth()   
    {
        return $this->container['authenticate'];
    }
    /**
     * Get Database Connector
     * @return Lego\Database\Connector;
     */
    public function getDBConnector()
    {
        return $this->container['db'];
    }
    /**
     * Get Database Adapter
     * @return \Illuminate\Database\ConnectionInterface
    */
    public function getDBAdapter($name = 'default')
    {
        return $this->getDBConnector()->getConnection($name);
    }
    /**
     *
     * @param array $methods
     * @param string $return
     */
    public function isAllowMethod($methods = ['*'], $return = false)
    {
        $result = false;
        if(!is_array($methods))
        {
            $methods = array($methods);
        }
        if(isset($methods[0]) && $methods[0] == '*')
        {
            $result = true;
        }
        if(in_array($this->request->getMethod(),$methods))
        {
            $result = true;
        }
        if($result)
        {
            return $result;
        }
        if($result == false)
        {
            throw new MethodNotAllowedException($this->request, $this->response, $methods);
        }
    }
}
?>