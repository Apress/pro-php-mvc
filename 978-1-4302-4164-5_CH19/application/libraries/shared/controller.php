<?php

namespace Shared
{
    use Framework\Events as Events;
    use Framework\Registry as Registry;

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

            // schedule disconnect from database 
            Events::add("framework.controller.destruct.after", function($name) {
                $database = Registry::get("database");
                $database->disconnect();
             });
            
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