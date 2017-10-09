<?php
namespace Lego\Exception; 
class UnauthorizedException extends \Exception
{
    protected $code = HTTP_CODE_FORBIDDEN;
}
?>