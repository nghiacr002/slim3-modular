<?php 
namespace Lego\Slim; 
use Detection\MobileDetect;
class Request extends \Slim\Http\Request
{
    protected $detection = null;
    /**
     * Check is request from mobile or not
     * @return boolean
     */ 
    public function isMobile()
    {
        return $this->getDetection()->isMobile();
    }
    /**
     * Check is request from table or not
     * @return boolean
     */
    public function isTablet()
    {
        return $this->getDetection()->isTablet();
    }
    /**
     * Get detection
     * @return \Detection\MobileDetect
     */
    public function getDetection()
    {
        if($this->detection)
        {
            return $this->detection;
        }
        $this->detection = new MobileDetect();
        return $this->detection;
    }
}