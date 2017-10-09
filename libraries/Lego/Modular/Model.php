<?php
namespace Lego\Modular; 
use \Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * @inheritdoc
 * @author nghia
 *
 */
class Model extends EloquentModel
{
   //TODO Later
   protected $cacheType = 'filesystem';
   protected $expiredTime = 300;//in seconds
   protected $cacheManager;
   public function __construct(array $attributes = [])
   {
       parent::__construct($attributes);
       $this->cacheManager = null;
   }
   /**
    * Get application cache pools manager
    * @return \Lego\Slim\Cache
    */
   public function getCacheManager()
   {
       if($this->cacheManager)
       {
           return $this->cacheManager;
       }
       $app = \Lego\Slim\App::getInstance();
       if(!$app->getContainer()->has('cache'))
       {
           return null;
       }
       $this->cacheManager = $app->getContainer()->get('cache');
       return $this->cacheManager;
   }
   
    /**
     * Get item(s) from cache and form to data
     * @param string $key
     * @param string $poolName
     * @return mixed
     */
   public function getFromCache($key, $poolName = 'filesystem')
   {
       return $this->getCacheManager()->get($key,$poolName);
   }
   /**
    * Save item to cache
    * @param string $key unique hash string as id of cache
    * @param array $data data to save to Cache
    * @param int $ttl time to live
    */
   public function saveToCache($key = null, $data = null, $ttl = null)
   {
       if(!$key)
       {
           $key = $this->getQualifiedKeyName().'_'. $this->getKey();
       }
       if($data == null)
       {
           $data = $this->toArray();
       }
       if(!$ttl)
       {
           $ttl = $this->expiredTime;
       }
       return $this->getCacheManager()->save($key,$data,$ttl,$this->cacheType);
   }
   /**
    * Set Expired Time
    * @param number $time expired time
    * @return \Lego\Modular\Model
    */
   public function setExpiredTime($time = 300)
   {
       $this->expiredTime = $time;
       return $this;
   }
   /**
    * Get expired time in seconds
    * @return number
    */
   public function getExpiredTime()
   {
       return $this->expiredTime;
   }
   /**
    * Set cache instance
    * @param string $instance
    * @return \Lego\Modular\Model
    */
   public function setCacheInstance($instance = "filesystem")
   {
       $this->cacheType = $instance;
       return $this;
   }
}
?>