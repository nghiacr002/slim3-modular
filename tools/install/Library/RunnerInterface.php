<?php 
namespace Lego\Installation\Library;
interface RunnerInterface
{
    /**
     * Get Information of runner
     * @return string description for content
     */
    public function getInfo();
    /**
     * Run the script
     */
    public function process(); 
    /**
     * Get runner version
     */
    public function getVersion(); 
    /**
     * Get release Date
     */
    public function getReleaseDate(); 
    
    
}