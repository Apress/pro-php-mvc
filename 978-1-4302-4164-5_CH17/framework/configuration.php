<?php

namespace Framework
{
    use Framework\Base as Base;
    use Framework\Events as Events;
    use Framework\Configuration as Configuration;
    use Framework\Configuration\Exception as Exception;
    
    class Configuration extends Base
    {
        /**
        * @readwrite
        */
        protected $_type;
        
        /**
        * @readwrite
        */
        protected $_options;
        
        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
        
        public function initialize()
        {
            if (!$this->type)
            {
                throw new Exception\Argument("Invalid type");
            }
            
            switch ($this->type)
            {
                case "ini":
                {
                    return new Configuration\Driver\Ini($this->options);
                    break;
                }
                default:
                {
                    throw new Exception\Argument("Invalid type");
                    break;
                }
            }
        }
    }
}