<?php
namespace Lego\Modular; 

use Interop\Container\ContainerInterface;
use Lego\Interfaces\ViewInterface;

class APIController extends Controller
{
    protected $mode = ViewInterface::MODE_RENDER_JSON;
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
        $this->view->setMode($this->mode);
        $this->setErrorHeader(0,"");
    }
    public function setErrorHeader($code = "", $message = "")
    {
        $this->view->setHeaderOptions(array( 
            'ErrorCode' => $code,
            'ErrorMessage' => $message)
        );
    }
}
?>