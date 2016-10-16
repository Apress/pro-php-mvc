<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace("Imagine");
    }

    protected function _initRouter()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $routes = array(
            "register" => array("register", "users", "register"),
            "login" => array("login", "users", "login"),
            "logout" => array("logout", "users", "logout"),
            "search" => array("search", "users", "search"),
            "profile" => array("profile", "users", "profile"),
            "settings" => array("settings", "users", "settings"),
            "friend" => array("friend/:id", "users", "friend"),
            "unfriend" => array("unfriend/:id", "users", "unfriend"),
        );
    
        foreach ($routes as $name => $route)
        {
            list($pattern, $controller, $action) = $route;
            
            $router->addRoute($name, new Zend_Controller_Router_Route($pattern, array("controller" => $controller, "action" => $action)));
        }
        
        $db = Zend_Db::factory(
            "pdo_mysql",
            array(
                "host" => "localhost",
                "username" => "prophpmvc",
                "password" => "prophpmvc",
                "dbname" => "prophpmvc-zendframework",
                "unix_socket" => "/Applications/MAMP/tmp/mysql/mysql.sock"
            )
        );
        
        Zend_Registry::set("db", $db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }
}