<?php

namespace Framework
{
    use Framework\ArrayMethods as ArrayMethods;
    use Framework\StringMethods as StringMethods;
    
    class Inspector
    {
        protected $_class;
        
        protected $_meta = array(
            "class" => array(),
            "properties" => array(),
            "methods" => array()
        );
        
        protected $_properties = array();
        protected $_methods = array();
        
        public function __construct($class)
        {
            $this->_class = $class;
        }
        
        protected function _getClassComment()
        {
            $reflection = new \ReflectionClass($this->_class);
            return $reflection->getDocComment();
        }
        
        protected function _getClassProperties()
        {
            $reflection = new \ReflectionClass($this->_class);
            return $reflection->getProperties();
        }
        
        protected function _getClassMethods()
        {
            $reflection = new \ReflectionClass($this->_class);
            return $reflection->getMethods();
        }
        
        protected function _getPropertyComment($property)
        {
            $reflection = new \ReflectionProperty($this->_class, $property);
            return $reflection->getDocComment();
        }
        
        protected function _getMethodComment($method)
        {
            $reflection = new \ReflectionMethod($this->_class, $method);
            return $reflection->getDocComment();
        }
        
        protected function _parse($comment)
        {
            $meta = array();
            $pattern = "(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_]*)";
            $matches = StringMethods::match($comment, $pattern);
            
            if ($matches != null)
            {
                foreach ($matches as $match)
                {
                    $parts = ArrayMethods::clean(
                       ArrayMethods::trim(
                            StringMethods::split($match, "[\s]", 2)
                        )
                    );
                    
                    $meta[$parts[0]] = true;
                    
                    if (sizeof($parts) > 1)
                    {
                        $meta[$parts[0]] = ArrayMethods::clean(
                            ArrayMethods::trim(
                                StringMethods::split($parts[1], ",")
                            )
                        );
                    }
                }
            }
            
            return $meta;
        }
        
        public function getClassMeta()
        {
            if (!isset($_meta["class"]))
            {
                $comment = $this->_getClassComment();
                
                if (!empty($comment))
                {
                    $_meta["class"] = $this->_parse($comment);
                }
                else
                {
                    $_meta["class"] = null;
                }
            }
            
            return $_meta["class"];
        }
        
        public function getClassProperties()
        {
            if (!isset($_properties))
            {
                $properties = $this->_getClassProperties();
                
                foreach ($properties as $property)
                {
                    $_properties[] = $property->getName();
                }
            }
            
            return $_properties;
        }
        
        public function getClassMethods()
        {
            if (!isset($_methods))
            {
                $methods = $this->_getClassMethods();
                
                foreach ($methods as $method)
                {
                    $_methods[] = $method->getName();
                }
            }
            
            return $_properties;
        }
        
        public function getPropertyMeta($property)
        {
            if (!isset($_meta["properties"][$property]))
            {
                $comment = $this->_getPropertyComment($property);
                
                if (!empty($comment))
                {
                    $_meta["properties"][$property] = $this->_parse($comment);
                }
                else
                {
                    $_meta["properties"][$property] = null;
                }
            }
            
            return $_meta["properties"][$property];
        }
        
        public function getMethodMeta($method)
        {    
            if (!isset($_meta["actions"][$method]))
            {
                $comment = $this->_getMethodComment($method);
                
                if (!empty($comment))
                {
                    $_meta["methods"][$method] = $this->_parse($comment);
                }
                else
                {
                    $_meta["methods"][$method] = null;
                }
            }
            
            return $_meta["methods"][$method];
        }
    }
}