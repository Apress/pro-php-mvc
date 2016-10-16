<?php

namespace Framework
{
    use Framework\Base as Base;
    use Framework\Events as Events;
    use Framework\Template as Template;
    use Framework\View\Exception as Exception;
    
    class View extends Base
    {
        /**
        * @readwrite
        */
        protected $_file;
        
        /**
        * @readwrite
        */
        protected $_data;
        
        /**
        * @read
        */
        protected $_template;
        
        public function __construct($options = array())
        {
            parent::__construct($options);
            
            $this->_template = new Template(array(
                "implementation" => new Template\Implementation\Extended()
            ));
        }
        
        public function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
        
        public function render()
        {
            if (!file_exists($this->file))
            {
                return "";
            }
            
            return $this
                ->template
                ->parse(file_get_contents($this->file))
                ->process($this->data);
        }
        
        public function get($key, $default = "")
        {
            if (isset($this->data[$key]))
            {
                return $this->data[$key];
            }
            return $default;
        }
        
        protected function _set($key, $value)
        {    
            if (!is_string($key) && !is_numeric($key))
            {
                throw new Exception\Data("Key must be a string or a number");
            }
        
            $data = $this->data;
            
            if (!$data)
            {
                $data = array();
            }
            
            $data[$key] = $value;
            $this->data = $data;
        }
        
        
        public function set($key, $value = null)
        {
            if (is_array($key))
            {
                foreach ($key as $_key => $value)
                {
                    $this->_set($_key, $value);
                }
                return $this;
            }
            
            $this->_set($key, $value);
            return $this;
        }
        
        public function erase($key)
        {
            unset($this->data[$key]);
            return $this;
        }
    }    
}