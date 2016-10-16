<?php

namespace Shared
{
    class Controller extends \Framework\Controller
    {
        /**
        * @readwrite
        */
        protected $_user;

        public function __construct($options = array())
        {
            parent::__construct($options);
    
            $database = \Framework\Registry::get("database");
            $database->connect();
            
            $session = \Framework\Registry::get("session");
            $user = unserialize($session->get("user", null));
            $this->setUser($user);
        }

        public function render()
        {
            if ($this->getUser())
            {
                if ($this->getActionView())
                {
                    $this->getActionView()
                        ->set("user", $this->getUser());
                }
                
                if ($this->getLayoutView())
                {
                    $this->getLayoutView()
                        ->set("user", $this->getUser());
                }
            }
                
            parent::render();
        }
    }
}