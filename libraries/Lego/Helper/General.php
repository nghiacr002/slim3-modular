<?php 
namespace Lego\Helper; 

use Psr\Http\Message\RequestInterface;
use Reflection;
use ReflectionObject;
class General
{
    /**
     * Generate random code
     * @param number $number
     * @return string
     */
    public static function generateTrackCode($number = 6)
    {
        return substr(md5(time().uniqid()),0,$number);
    }
    /**
     * Checking version of following format http://php.net/manual/en/function.version-compare.php
     * @param string $version
     * @param string $min
     * @param string $max
     * @return boolean
     */
    public static function isCompatibleVersion($version, $min = null, $max = null)
    {
        if($min)
        {
            if(version_compare($version, $min) < 0)
            {
                return false;
            }
        }
        if($max)
        {
            if(version_compare($version, $max) > 0)
            {
                return false;
            }
        }
        return true;
    }
    /**
     * Auto pull data from request to object
     * @param RequestInterface $request
     * @param Object $object
     */
    public static function mapDataToObject(RequestInterface $request, $object)
    {
        if(!$object)
        {
            return $object;
        }
        $properties = Reflection::export(new ReflectionObject($object));
        //TODO
        //not complete
        //d($properties);die();
    }
    
}