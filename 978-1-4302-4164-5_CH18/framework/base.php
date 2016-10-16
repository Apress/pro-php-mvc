<?php

namespace Framework
{
    use Framework\Inspector as Inspector;
    use Framework\ArrayMethods as ArrayMethods;
    use Framework\StringMethods as StringMethods;
    use Framework\Core\Exception as Exception;
    
    class Base
    {
        private $_inspector;
        
        public function __construct($options = array())
        {
            $this->_inspector = new Inspector($this);
            
            if (is_array($options) || is_object($options))
            {
                foreach ($options as $key => $value)
                {
                    $key = ucfirst($key);
                    $method = "set{$key}";
                    $this->$method($value);
                }
            }
        }
        
        public function __call($name, $arguments)
        {
            if (empty($this->_inspector))
            {
                throw new Exception("Call parent::__construct!");
            }
            
            $getMatches = StringMethods::match($name, "^get([a-zA-Z0-9]+)$");
            if (sizeof($getMatches) > 0)
            {
                $normalized = lcfirst($getMatches[0]);
                $property = "_{$normalized}";
                
                if (property_exists($this, $property))
                {
                    $meta = $this->_inspector->getPropertyMeta($property);
                    
                    if (empty($meta["@readwrite"]) && empty($meta["@read"]))
                    {
                        throw $this->_getExceptionForWriteonly($normalized);
                    }
                    
                    if (isset($this->$property))
                    {
                        return $this->$property;
                    }
                    
                    return null;
                }
            }
            
            $setMatches = StringMethods::match($name, "^set([a-zA-Z0-9]+)$");
            if (sizeof($setMatches) > 0)
            {
                $normalized = lcfirst($setMatches[0]);
                $property = "_{$normalized}";
                
                if (property_exists($this, $property))
                {
                    $meta = $this->_inspector->getPropertyMeta($property);
                    
                    if (empty($meta["@readwrite"]) && empty($meta["@write"]))
                    {
                        throw $this->_getExceptionForReadonly($normalized);
                    }
                    
                    $this->$property = $arguments[0];
                    return $this;
                }
            }
            
            throw $this->_getExceptionForImplementation($name);
        }
        
        public function __get($name)
        {
            $function = "get".ucfirst($name);
            return $this->$function();
        }
        
        public function __set($name, $value)
        {
            $function = "set".ucfirst($name);
            return $this->$function($value);
        }
        
        protected function _getExceptionForReadonly($property)
        {
            return new Exception\ReadOnly("{$property} is read-only");
        }
        
        protected function _getExceptionForWriteonly($property)
        {
            return new Exception\WriteOnly("{$property} is write-only");
        }
        
        protected function _getExceptionForProperty()
        {
            return new Exception\Property("Invalid property");
        }
        
        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Argument("{$method} method not implemented");
        }
    }    
}