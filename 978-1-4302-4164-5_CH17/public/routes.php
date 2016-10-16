<?php

// define routes

$routes = array(
    array(
        "pattern" => "register",
        "controller" => "users",
        "action" => "register"
    ),
    array(
        "pattern" => "login",
        "controller" => "users",
        "action" => "login"
    ),
    array(
        "pattern" => "logout",
        "controller" => "users",
        "action" => "logout"
    ),
    array(
        "pattern" => "search",
        "controller" => "users",
        "action" => "search"
    ),
    array(
        "pattern" => "profile",
        "controller" => "users",
        "action" => "profile"
    ),
    array(
        "pattern" => "settings",
        "controller" => "users",
        "action" => "settings"
    ),
    array(
        "pattern" => "unfriend/:id",
        "controller" => "users",
        "action" => "friend"
    ),
    array(
        "pattern" => "friend/:id",
        "controller" => "users",
        "action" => "friend"
    )
);

// add defined routes

foreach ($routes as $route)
{
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals

unset($routes);
