<?php

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
            
            Events::fire("framework.router.controller.before", array($controller, $parameters));
            
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
            
            Events::fire("framework.router.controller.after", array($controller, $parameters));
            
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
            
            Events::fire("framework.router.beforehooks.before", array($action, $parameters));
            
            $hooks($methodMeta, "@before");
            
            Events::fire("framework.router.beforehooks.after", array($action, $parameters));
            Events::fire("framework.router.action.before", array($action, $parameters));
            
            call_user_func_array(array(
                $instance,
                $action
            ), is_array($parameters) ? $parameters : array());
            
            Events::fire("framework.router.action.after", array($action, $parameters));
            Events::fire("framework.router.afterhooks.before", array($action, $parameters));
            
            $hooks($methodMeta, "@after");
            
            Events::fire("framework.router.afterhooks.after", array($action, $parameters));
            
            // unset controller
            
            Registry::erase("controller");
        }
        
        public function dispatch()
        {
            $url= $this->url;
            $parameters = array();
            $controller = "index";
            $action = "index";
        
            Events::fire("framework.router.dispatch.before", array($url));
                    
            foreach ($this->_routes as $route)
            {
                $matches = $route->matches($url);
                if ($matches)
                {
                    $controller = $route->controller;
                    $action = $route->action;
                    $parameters = $route->parameters;
                    
                    Events::fire("framework.router.dispatch.after", array($url, $controller, $action, $parameters));
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
            
            Events::fire("framework.router.dispatch.after", array($url, $controller, $action, $parameters));
            $this->_pass($controller, $action, $parameters);
        }
    }
}