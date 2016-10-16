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
    ),
    array(
        "pattern" => "fonts/:id",
        "controller" => "files",
        "action" => "fonts"
    ),
    array(
        "pattern" => "thumbnails/:id",
        "controller" => "files",
        "action" => "thumbnails"
    ),
    array(
        "pattern" => "users/edit/:id",
        "controller" => "users",
        "action" => "edit"
    ),
    array(
        "pattern" => "users/delete/:id",
        "controller" => "users",
        "action" => "delete"
    ),
    array(
        "pattern" => "users/undelete/:id",
        "controller" => "users",
        "action" => "undelete"
    ),
    array(
        "pattern" => "files/delete/:id",
        "controller" => "files",
        "action" => "delete"
    ),
    array(
        "pattern" => "files/undelete/:id",
        "controller" => "files",
        "action" => "undelete"
    )
);

// add defined routes

foreach ($routes as $route)
{
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals

unset($routes);
