<?php
namespace Lego\Exception; 
class UnAuthenticatedException extends \Exception
{
    protected $code = HTTP_CODE_FORBIDDEN;
}
?>