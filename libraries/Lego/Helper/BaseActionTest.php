<?php 
namespace Lego\Helper; 
use Lego\Slim\App;
use Slim\Http\Request;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use Slim\Http\Environment;

class BaseActionTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $env;
    protected $headers;
    protected $endPoint;
    protected $uri;
    protected $basePath;
    protected $serverName;
    protected $settings;
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name,$data,$dataName);
        $this->initApp();
    }
    /**
     * Init default environment, settings & application;
     */
    protected function initApp()
    {
        $settings = require APP_SETTING_PATH. 'application.php';
        if($settings['settings']['mode'] == "development")
        {
            require APP_SOURCE_PATH.'Settings/development.php';
        }
        else
        {
            require APP_SOURCE_PATH.'Settings/production.php';
        }
        $settings['settings']['logger']['path'] = APP_LOG_PATH. 'unit-test.log';
        $this->settings = $settings;
        $this->app = new App($this->settings);
        $this->env = array(
            "CONTENT_TYPE" => "application/json",
            "HTTP_USER_AGENT" => "Lego API Test",
            "SERVER_PORT" => 80
        );
        
    }
    /**
     * 
     * {@inheritDoc}
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        printf("\n_______________________________________\n\n");
    }
    /**
     * Assign Headers to Request
     * @param RequestInterface $request
     * @param array $headers
     * @return \Psr\Http\Message\RequestInterface
     */    
    protected function setHeadersToRequest(RequestInterface $request, $headers = [])
    {
       $headers = array_merge($this->headers,$headers);
       if(count($headers))
       {
           foreach($headers as $key => $value)
           {
               $request = $request->withHeader($key, $value);
           }
       }
       return $request; 
    }
    protected function request($method, $uri, $env = [], $headers = [])
    {
        $env['REQUEST_METHOD'] = $method;
        $env['REQUEST_URI'] = $this->getURI($uri);
        $request = $this->createRequest($env, $headers);
        return $this->process($request);
    }
    /**
     * Process request with DELETE method
     * @param string $uri
     * @param array $env
     * @param array $headers
     * @param array $body
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function delete($uri, $env = [], $headers = [] , $body = [])
    {
        return $this->request("DELETE", $uri, $env, $headers);
    }
    /**
     * Process request with PUT method
     * @param string $uri
     * @param array $env
     * @param array $headers
     * @param array $body
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function put($uri, $env = [], $headers = [] , $body = [])
    {
        return $this->request("PUT", $uri, $env, $headers);   
    }
    /**
     * Process request with GET method
     * @param string $uri
     * @param array $env
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function get($uri, $env = [],  $headers = [])
    {
        return $this->request("GET", $uri, $env, $headers);   
    }
    /**
     * Process request with POST method
     * @param string $uri
     * @param array $env
     * @param array $headers
     * @param array $body
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function post($uri, $env = [], $headers = [] , $body = [])
    {
        return $this->request("POST", $uri, $env, $headers);   
    }
    /**
     * Create Mock Request from ENV and Headers
     * @param array $env
     * @param array $headers
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createRequest($env, $headers)
    {
        $this->env['HTTP_HOST'] = !empty($this->endPoint) ? $this->endPoint : "localhost";
        $this->env['SERVER_NAME']  = !empty($this->serverName) ? $this->serverName : "localhost";
        $this->env['SCRIPT_NAME'] = !empty($this->basePath) ? $this->basePath."/index.php" : "";
        $env = array_merge($this->env,$env);
        $env = Environment::mock($env);
        $request = Request::createFromEnvironment($env);
        $request = $this->setHeadersToRequest($request,$headers);
        return $request;
    }
    /**
     * Process mock request
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function process(RequestInterface $request)
    {
        $this->app->getContainer()['request'] = $request;
        return $this->app->run(true);
    }
    /**
     * Get full request URI
     * @param string $uri
     * @return string
     */
    protected function getURI($uri)
    {
        return $this->basePath . $uri;
    }
}