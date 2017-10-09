<?php 
namespace Lego\Slim;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Common\CacheItem;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Cache\Adapter\Predis\PredisCachePool;

class Cache
{
    private $pools;
    public function __construct($config = [])
    {
        if(isset($config['filesystem']))
        {
            try
            {
                $filesystemAdapter = new Local($config['filesystem']['path']);
                $filesystem        = new Filesystem($filesystemAdapter);
                $pool = new FilesystemCachePool($filesystem,'');
                $this->pools['filesystem'] = $pool;
            }
            catch(\Exception $ex)
            {
                //do nothing
            }
        }
        if(isset($config['redis']))
        {
            foreach($config['redis'] as $name => $configRedis)
            {
                try
                {
                    $endpoint = $configRedis['protocol'].':/'. $configRedis['host'].':'. $configRedis['port'];
                    $client = new \Predis\Client($endpoint);
                    $pool = new PredisCachePool($client);
                    $this->pools['redis'][$name] = $pool;
                }
                catch(\Exception $ex)
                {
                    //do nothing
                }
            }
           
        }
    }
   
    /**
     * Get pool cache
     * @param string $name
     * @return \Cache\Adapter\Common\AbstractCachePool
     */
    public function getPool($name)
    {
        return isset($this->pools[$name]) ? $this->pools[$name] : null;
    }
    /**
     * 
     * @return \Cache\Adapter\Filesystem\FilesystemCachePool
     */
    public function fs()
    {
        return $this->getPool('filesystem');
    }
    /**
     * 
     * @return \Cache\Adapter\Predis\PredisCachePool
     */
    public function redis($instance = 'default')
    {
        $redis =  $this->getPool('redis');
        return isset($redis[$instance]) ? $redis[$instance] : null;
    }
    /**
     * save data to cache instance
     * @param string $key
     * @param array $data
     * @param int $ttl - Time to live in seconds
     */
    public function save($key, $data = [], $ttl = 3000, $poolName = 'filesystem')
    {
        $cacheItem = new CacheItem($key);
        $cacheItem->set($data);
        $cacheItem->expiresAfter($ttl);
        if($poolName == null)
        {
            //save all
            foreach($this->pools as $name => $instance)
            {
                $instance->save($cacheItem);
            }
        }
        else
        {
            $instance = $this->getPool($poolName);
            $instance->save($cacheItem);
        }
    }
    public function get($key, $poolName = 'filesystem')
    {
        return $this->getPool($poolName)->get($key);
    }
}