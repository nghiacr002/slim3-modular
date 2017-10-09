<?php 
use Lego\Helper\BaseActionTest;

/**
 * Class to test Hello Action Function in Modules/Core/Controller/IndexController
 * @author nghia
 *
 */
class HelloActionTest extends BaseActionTest
{
    protected function setUp()
    {
        //do your custom code here
        $this->uri = "/core/hello";
        $this->headers['Content-Type'] = "application/json";
        //$this->endPoint = "styl.dev";
        //$this->serverName = 'styl.dev';
        //$this->basePath = "/api/public";
        //$this->env['HTTP_ACCEPT'] = "application/json";
    }
    /**
     * Testcase To check only allow GET method
     */
    public function testAllowMethod()
    {
        $env = array(
            'QUERY_STRING'=>'v=12111'
        );
        $methods = ["PUT","POST","PATCH","DELETE"];
        foreach($methods as $method)
        {
            $response =  $this->request($method,$this->uri,$env);
            $this->assertSame($response->getStatusCode(), HTTP_CODE_METHOD_NOT_ALLOWED, $method."--". $response->getBody());
        }
    }
    /**
     * Testcase to check return in JSON format
     */
    public function testJsonFormat()
    {
        $env = array(
            'QUERY_STRING'=>'v=1111'
        );
        $response =  $this->get($this->uri,$env);
        $this->assertSame((string)$response->getBody(), '{"v":"1111"}');
    }
    /**
     * Testcase to check correct response from input
     */
    public function testCorrectInput()
    {
        $value = 10;
        $env = array(
            'QUERY_STRING'=>'v='.$value
        );
        $response =  $this->get($this->uri,$env);
        $data = (string)$response->getBody();
        $data = json_decode($data,true);
        $this->assertArrayHasKey('v', $data);
        $this->assertEquals(isset($data['v']) ? $data['v'] : null, $value);
    }
    
}