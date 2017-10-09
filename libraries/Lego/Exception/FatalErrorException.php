<?php
namespace Lego\Exception; 

class FatalErrorException extends \Exception
{
    protected $code = 505;
    protected $message = "Runtime Fatal Error";
    protected $internalMessage = "";
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    public function setInternalMessage($message = "")
    {
        $this->internalMessage = $message;
        return $this;
    }
    public function getInternalMessage()
    {
        return $this->internalMessage;
    }
}
?>