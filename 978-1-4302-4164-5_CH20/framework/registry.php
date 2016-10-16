<?php

namespace Framework
{
    class Registry
    {
        private static $_instances = array();
        
        private function __construct()
        {
            // do nothing
        }
        
        private function __clone()
        {
            // do nothing
        }
        
        public static function get($key, $default = null)
        {
            if (isset(self::$_instances[$key]))
            {
                return self::$_instances[$key];
            }
            return $default;
        }
        
        public static function set($key, $instance = null)
        {
            self::$_instances[$key] = $instance;
        }
        
        public static function erase($key)
        {
            unset(self::$_instances[$key]);
        }
    }
}