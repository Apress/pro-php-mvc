<?php

namespace Shared
{
    class Markup
    {
        public function __construct()
        {
            // do nothing
        }
        
        public function __clone()
        {
            // do nothing
        }
        
        public static function errors($array, $key, $separator = "<br />", $before = "<br />", $after = "")
        {
            if (isset($array[$key]))
            {
                return $before.join($separator, $array[$key]).$after;
            }
            return "";
        }
    }
}
