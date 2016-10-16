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

        /**
        * @protected
        */
        public function _admin()
        {
            if (!$this->user->admin)
            {
                throw new Router\Exception\Controller("Not a valid admin user account");
            }
        }

        /**
        * @protected
        */
        public function _secure()
        {
            $user = $this->getUser();
            if (!$user)
            {
                header("Location: /login.html");
                exit();
            }
        }

        public static function redirect($url)
        {
            header("Location: {$url}");
            exit();
        }
        
        public function setUser($user)
        {
            $session = Registry::get("session");
            
            if ($user)
            {
                $session->set("user", $user->id);
            }
            else
            {
                $session->erase("user");    
            }
            
            $this->_user = $user;
            return $this;
        }


        public function __construct($options = array())
        {
            parent::__construct($options);
    
            $database = \Framework\Registry::get("database");
            $database->connect();
            
            // schedule: load user from session 
            Events::add("framework.router.beforehooks.before", function($name, $parameters) {
                $session = Registry::get("session");
                $controller = Registry::get("controller");
                $user = $session->get("user");
                
                if ($user)
                {
                    $controller->user = \User::first(array(
                        "id = ?" => $user
                    ));
                }
            });

            // schedule: save user to session 
            Events::add("framework.router.afterhooks.after", function($name, $parameters) {
                $session = Registry::get("session");
                $controller = Registry::get("controller");
                
                if ($controller->user)
                {
                    $session->set("user", $controller->user->id);
                }
            });

            // schedule disconnect from database 
            Events::add("framework.controller.destruct.after", function($name) {
                $database = Registry::get("database");
                $database->disconnect();
            });
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