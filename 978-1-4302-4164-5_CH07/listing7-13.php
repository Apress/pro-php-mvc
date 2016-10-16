<?php 

include("_base.php");
include("_exceptions.php");
include("_inspector.php");
include("_methods.php");
include("_registry.php");
include("router.php");
include("controller.php");

class Home extends Framework\Controller
{
    /**
    * @once
    * @protected
    */
    public function init()
    {
        echo "init";
    }
    
    /**
    * @protected
    */
    public function authenticate()
    {
        echo "authenticate";
    }
    
    /**
    * @before init, authenticate, init
    * @after notify
    */
    public function index()
    {
        echo "hello world!";
    }
    
    /**
    * @protected
    */
    public function notify()
    {
        echo "notify";
    }
}


$router = new Framework\Router();
$router->addRoute(
    new Framework\Router\Route\Simple(array(
        "pattern" => ":name/profile",
        "controller" => "home",
        "action" => "index"
    ))
);

$router->url = "chris/profile";
$router->dispatch();