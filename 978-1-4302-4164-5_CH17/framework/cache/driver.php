<?php

namespace Framework\Cache
{
    use Framework\Base as Base;
    use Framework\Cache\Exception as Exception;
    
    class Driver extends Base
    {
        public function initialize()
        {
            return $this;
        }
        
        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
    }
}