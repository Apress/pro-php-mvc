<?php

namespace Framework\Session
{
    use Framework\Base as Base;
    use Framework\Session\Exception as Exception;
    
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