<?php 

namespace Framework\Router
{
    use Framework\Base as Base;
    use Framework\Router\Exception as Exception;
    
    class Route extends Base
    {
        /**
        * @readwrite
        */
        protected $_pattern;
        
        /**
        * @readwrite
        */
        protected $_controller;
        
        /**
        * @readwrite
        */
        protected $_action;
        
        /**
        * @readwrite
        */
        protected $_parameters = array();
        
        public function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
    }
}

namespace Framework\Router\Route
{
    use Framework\Router as Router;
    
    class Regex extends Router\Route
    {    
        /**
        * @readwrite
        */
        protected $_keys;
        
        public function matches($url)
        {
            $pattern = $this->pattern;
            
            // check values 
            preg_match_all("#^{$pattern}$#", $url, $values);
            
            if (sizeof($values) && sizeof($values[0]) && sizeof($values[1]))
            {
                // values found, modify parameters and return 
                $derived = array_combine($this->keys, $values[1]);
                $this->parameters = array_merge($this->parameters, $derived);
                
                return true;
            }
            
            return false;
        }
    }
}

namespace Framework\Router\Route
{
    use Framework\Router as Router;
    use Framework\ArrayMethods as ArrayMethods;
    
    class Simple extends Router\Route
    {
        public function matches($url)
        {
            $pattern = $this->pattern;
            
            // get keys
            preg_match_all("#:([a-zA-Z0-9]+)#", $pattern, $keys);
            
            if (sizeof($keys) && sizeof($keys[0]) && sizeof($keys[1]))
            {
                $keys = $keys[1];
            }
            else
            {
                // no keys in the pattern, return a simple match
                return preg_match("#^{$pattern}$#", $url);
            }
            
            // normalize route pattern
            $pattern = preg_replace("#(:[a-zA-Z0-9]+)#", "([a-zA-Z0-9-_]+)", $pattern);
            
            // check values
            preg_match_all("#^{$pattern}$#", $url, $values);
            
            if (sizeof($values) && sizeof($values[0]) && sizeof($values[1]))
            {
                // unset the matched url
                unset($values[0]);
                
                // values found, modify parameters and return
                $derived = array_combine($keys, ArrayMethods::flatten($values));
                $this->parameters = array_merge($this->parameters, $derived);
                
                return true;
            }
            
            return false;
        }
    }
}

namespace Framework
{
    use Framework\Base as Base;
    use Framework\Events as Events;
    use Framework\Registry as Registry;
    use Framework\Inspector as Inspector;
    use Framework\Router\Exception as Exception;
    
    class Router extends Base
    {
        /**
        * @readwrite
        */
        protected $_url;
        
        /**
        * @readwrite
        */
        protected $_extension;
        
        /**
        * @read
        */
        protected $_controller;
        
        /**
        * @read
        */
        protected $_action;
        
        protected $_routes = array();
        
        public function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
            
        public function addRoute($route)
        {
            $this->_routes[] = $route;
            return $this;
        }
        
        public function removeRoute($route)
        {
            foreach ($this->_routes as $i => $stored)
            {
                if ($stored == $route)
                {
                    unset($this->_routes[$i]);
                }
            }
            return $this;
        }
        
        public function getRoutes()
        {
            $list = array();
            
            foreach ($this->_routes as $route)
            {
                $list[$route->pattern] = get_class($route);
            }
            
            return $list;
        }
        
        protected function _pass($controller, $action, $parameters = array())
        {
            $name = ucfirst($controller);
            
            $this->_controller = $controller;
            $this->_action = $action;
            
            try
            {
                $instance = new $name(array(
                    "parameters" => $parameters
                ));
                Registry::set("controller", $instance);
            }
            catch (\Exception $e)
            {
                throw new Exception\Controller("Controller {$name} not found");
            }
            
            if (!method_exists($instance, $action))
            {
                $instance->willRenderLayoutView = false;
                $instance->willRenderActionView = false;
                
                throw new Exception\Action("Action {$action} not found");
            }
                
            $inspector = new Inspector($instance);
            $methodMeta = $inspector->getMethodMeta($action);
            
            if (!empty($methodMeta["@protected"]) || !empty($methodMeta["@private"]))
            {
                throw new Exception\Action("Action {$action} not found");
            }
            
            $hooks = function($meta, $type) use ($inspector, $instance)
            {
                if (isset($meta[$type]))
                {
                    $run = array();
                    
                    foreach ($meta[$type] as $method)
                    {
                        $hookMeta = $inspector->getMethodMeta($method);
                        
                        if (in_array($method, $run) && !empty($hookMeta["@once"]))
                        {
                            continue;
                        }
                        
                        $instance->$method();
                        $run[] = $method;
                    }
                }
            };
            
            $hooks($methodMeta, "@before");
            
            call_user_func_array(array(
                $instance,
                $action
            ), is_array($parameters) ? $parameters : array());
            
            $hooks($methodMeta, "@after");
            
            // unset controller
            
            Registry::erase("controller");
        }
        
        public function dispatch()
        {
            $url= $this->url;
            $parameters = array();
            $controller = "index";
            $action = "index";
                    
            foreach ($this->_routes as $route)
            {
                $matches = $route->matches($url);
                if ($matches)
                {
                    $controller = $route->controller;
                    $action = $route->action;
                    $parameters = $route->parameters;
                    
                    $this->_pass($controller, $action, $parameters);
                    return;
                }
            }
                    
            $parts = explode("/", trim($url, "/"));
            
            if (sizeof($parts) > 0)
            {
                $controller = $parts[0];
                
                if (sizeof($parts) >= 2)
                {
                    $action = $parts[1];
                    $parameters = array_slice($parts, 2);
                }
            }
            
            $this->_pass($controller, $action, $parameters);
        }
    }
}