<?php

$get = function($url)
{
    $request = new Framework\Request();
    return $request->get("http://".$_SERVER["HTTP_HOST"]."/{$url}");
};

$has = function($html, $fields)
{
    foreach ($fields as $field)
    {
        if (!stristr($html, "name=\"{$field}\""))
        {
            return false;
        }
    }
    
    return true;
};

Framework\Test::add(
    function() use ($get, $has)
    {
        $html = $get("register.html");
        $status = $has($html, array(
            "first",
            "last",
            "email",
            "password",
            "photo",
            "register"
        ));
        
        return $status;
    },
    "Register form has required fields",
    "Forms/Users"
);

Framework\Test::add(
    function() use ($get, $has)
    {
        $html = $get("login.html");
        $status = $has($html, array(
            "email",
            "password",
            "login"
        ));
        
        return $status;
    },
    "Login form has required fields",
    "Forms/Users"
);

Framework\Test::add(
    function() use ($get, $has)
    {
        $html = $get("search.html");
        $status = $has($html, array(
            "query",
            "order",
            "direction",
            "page",
            "limit",
            "search"
        ));
        
        return $status;
    },
    "Search form has required fields",
    "Forms/Users"
);