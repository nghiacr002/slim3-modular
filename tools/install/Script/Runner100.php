<?php 
namespace Lego\Installation\Script; 
use Lego\Installation\Library\RunnerInterface;

class Runner100 implements RunnerInterface
{
    protected $version = "1.0.0";
    protected $releaseDate = "20/8/2017";
    public function process()
    {
        
    }

    public function getInfo()
    {
        
    }
    public function getVersion()
    {
        return $this->version;
    }
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }    
}
