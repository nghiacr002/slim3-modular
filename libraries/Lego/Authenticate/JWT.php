<?php 
namespace Lego\Authenticate; 
use Lego\Interfaces\AuthenticateInterface;
use Psr\Http\Message\RequestInterface;
use Firebase\JWT as FirebaseJWT;
class JWT implements AuthenticateInterface
{
    private $isAuth; 
    private $config;
    private $token;
    private $verifiedInfo;
    public function __construct()
    {
        $this->isAuth = false;
    }
    public function isAuth()
    {
        return $this->isAuth;
    }
    public function verify(RequestInterface $request)
    {
        $name = $this->getHeaderName(); 
        $token = $request->getHeaderLine($name);
        $key = $this->getSecretKey();
        $algorithm = isset($this->config['algorithm']) ? $this->config['algorithm'] : "";
        if(!is_array($algorithm))
        {
            $algorithm = array($algorithm);
        }
        try
        {
            $leeway = isset($this->config['leeway']) ? $this->config['leeway'] : 0;
            FirebaseJWT\JWT::$leeway = $leeway;
            $decode = FirebaseJWT\JWT::decode($token, $key, $algorithm);
            $decode = (array)$decode;
            $this->isAuth = true;
            $this->verifiedInfo = $decode;
            return true;
        }
        catch(\Exception $ex)
        {
            //d($ex->getMessage());die();
            //catch and force return to none verification
        }
        return false;
    }
    public function getVerifiedInfo()
    {
        return $this->verifiedInfo;
    }
    public function requestAccessToken($authInfo = [])
    {
        $defaultToken = $this->config['token'];
        $key = $this->getSecretKey();
        $algorithm = isset($this->config['algorithm']) ? $this->config['algorithm'] : "";
        $authInfo = array_merge($defaultToken,$authInfo); 
        $accesToken = FirebaseJWT\JWT::encode($authInfo, $key, $algorithm);
        return $accesToken;
    }
    public function getHeaderName()
    {
        $name = isset($this->config['headerName']) ? $this->config['headerName'] : "Authorization"; 
        return $name; 
    }
    public function setConfig($config = [])
    {
        $this->config = $config;
        return $this;
    }
    public function getConfig()
    {
        return $this->config;
    }
    public function getMethodName()
    {
        return "jwt";
    }
    /**
     * Get secret key from configuration
     * @return string
     */
    public function getSecretKey()
    {
        return isset($this->config['key']) ? $this->config['key'] : "";
    }
    
}