<?php

namespace Framework
{
    use Framework\Base as Base;
    use Framework\Events as Events;
    use Framework\Session as Session;
    use Framework\Registry as Registry;
    use Framework\Session\Exception as Exception;
    
    class Session extends Base
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
            Events::fire("framework.session.initialize.before", array($this->type, $this->options));
            
            if (!$this->type)
            {
                $configuration = Registry::get("configuration");
                
                if ($configuration)
                {
                    $configuration = $configuration->initialize();
                    $parsed = $configuration->parse("configuration/session");
                    
                    if (!empty($parsed->session->default) && !empty($parsed->session->default->type))
                    {
                        $this->type = $parsed->session->default->type;
                        unset($parsed->session->default->type);
                        $this->options = (array) $parsed->session->default;
                    }
                }
            }
            
            if (!$this->type)
            {
                throw new Exception\Argument("Invalid type");
            }
            
            Events::fire("framework.session.initialize.after", array($this->type, $this->options));
            
            switch ($this->type)
            {
                case "server":
                {
                    return new Session\Driver\Server($this->options);
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