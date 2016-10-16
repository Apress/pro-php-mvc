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
            Events::fire("framework.configuration.initialize.before", array($this->type, $this->options));
            
            if (!$this->type)
            {
                throw new Exception\Argument("Invalid type");
            }
            
            Events::fire("framework.configuration.initialize.after", array($this->type, $this->options));
            
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