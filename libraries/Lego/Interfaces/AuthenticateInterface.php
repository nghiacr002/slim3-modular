<?php
namespace Lego\Interfaces;
use Psr\Http\Message\RequestInterface;

/**
 * Interface to declare render information
 * @author nghia
 *
 */
interface AuthenticateInterface
{
    /**
     * Check the action is allow to process or not
     */
    public function isAuth(); 
    /**
     * Verify request with access token
     * @param RequestInterface $route
     * @return true|false 
     */
    public function verify(RequestInterface $request); 
    /**
     * Set configuration of Authenticate Method
     * @param array $configs
     */
    public function setConfig($configs = []);
    /**
     * Authenticate client with request token
     * @param array|mixed $authInfo
     * @return string access token
     */
    public function requestAccessToken($authInfo = []);
    /**
     * Get Authenticate Configuration
     */
    public function getConfig();
    /**
     * Get header defined key name
     */
    public function getHeaderName();
    /**
     * get name of authenticate method
     * @return string name
     */
    public function getMethodName();
    /**
     * Get verified information from header
     * @return array|mixed information 
     */
    public function getVerifiedInfo();
    
}
