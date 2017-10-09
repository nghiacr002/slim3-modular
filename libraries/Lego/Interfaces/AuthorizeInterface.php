<?php
namespace Lego\Interfaces;
/**
 * Authorize for user action base on PSR-7 
 * @author nghia
 *
 */
interface AuthorizeInterface
{
    /**
     * Check role is allowed to access resource action or not
     * @param string $role
     * @param string $resource
     * @param string $action
     */
    public function isAllowed($role, $resource, $action = null);
    /**
     * Check role is denied to access resource action or not
     * @param string $role
     * @param string $resource
     * @param string $action
     */
    public function isDenied($role, $resource, $action = null); 
    /**
     * Add role to resource and action
     * @param string $role
     * @param string $resource
     * @param string $action
     */
    public function addRole($role, $resource, $action = null);
    /**
     * Return all of assigned roles
     */
    public function getRoles(); 
}