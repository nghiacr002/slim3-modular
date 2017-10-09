<?php
namespace Lego\Authorize;
use Lego\Interfaces\AuthorizeInterface;

class ACL implements AuthorizeInterface
{
        
    public function isAllowed($role, $resource, $action = null)
    {
        
    }

    public function isDenied($role, $resource, $action = null)
    {
        
    }

    public function addRole($role, $resource, $action = null)
    {
        
    }

    public function getRoles()
    {
        
    }    
}