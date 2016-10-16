<?php

namespace Framework\Cache\Driver
{
    use Framework\Cache as Cache;
    use Framework\Cache\Exception as Exception;
    
    class Memcached extends Cache\Driver
    {
        protected $_service;
        
        /**
        * @readwrite
        */
        protected $_host = "127.0.0.1";
        
        /**
        * @readwrite
        */
        protected $_port = "11211";
        
        /**
        * @readwrite
        */
        protected $_isConnected = false;
        
        protected function _isValidService()
        {
            $isEmpty = empty($this->_service);
            $isInstance = $this->_service instanceof \Memcache;
            
            if ($this->isConnected && $isInstance && !$isEmpty)
            {
                return true;
            }
            
            return false;
        }
        
        public function connect()
        {
            try
            {
                $this->_service = new \Memcache();
                $this->_service->connect(
                    $this->host,
                    $this->port
                );
                $this->isConnected = true;
            }
            catch (\Exception $e)
            {
                throw new Exception\Service("Unable to connect to service");
            }
            
            return $this;
        }
        
        public function disconnect()
        {
            if ($this->_isValidService())
            {
                $this->_service->close();
                $this->isConnected = false;
            }
            
            return $this;
        }
        
        public function get($key, $default = null)
        {
            if (!$this->_isValidService())
            {
                throw new Exception\Service("Not connected to a valid service");
            }
        
            $value = $this->_service->get($key, MEMCACHE_COMPRESSED);
            
            if ($value)
            {
                return $value;
            }
            
            return $default;
        }
        
        public function set($key, $value, $duration = 120)
        {
            if (!$this->_isValidService())
            {
                throw new Exception\Service("Not connected to a valid service");
            }
        
            $this->_service->set($key, $value, MEMCACHE_COMPRESSED, $duration);
            return $this;
        }
        
        public function erase($key)
        {
            if (!$this->_isValidService())
            {
                throw new Exception\Service("Not connected to a valid service");
            }
            
            $this->_service->delete($key);
            return $this;
        }
    }
}