<?php 
namespace Lego\Helper;
/**
 * Shortcut functions to get data from containner
 * @author nghia
 *
 */
trait GetDataFromContainerTrait
{
    /**
     * Get all router configurations
     * @return NULL|mixed
     */
    public function getRouteConfigs()
    {
        return $this->getSetting('routerConfigs');
    }
    /**
     * Get router detail
     * @param mixed $name
     * @return NULL
     */
    public function getRouterConfig($name)
    {
        $configs = $this->getRouteConfigs(); 
        return isset($configs[$name]) ? $configs[$name] : null;
    }
    /**
     * Get all application settings
     * @return array
     */
    public function getSettings()
    {
        return $this->container['settings'];
    }
    /**
     * Get detail setting by key
     * @param string $key
     * @return NULL|mixed
     */
    public function getSetting($key)
    {
        $settings = $this->getSettings(); 
        return isset($settings[$key]) ? $settings[$key] : null;
    }
}