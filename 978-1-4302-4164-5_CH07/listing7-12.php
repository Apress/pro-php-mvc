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
    public function index()
    {
        echo "here";
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