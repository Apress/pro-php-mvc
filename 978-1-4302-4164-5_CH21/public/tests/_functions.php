<?php

$post = function($url, $data)
{
    $request = new Framework\Request();
    return $request->post("http://".$_SERVER["HTTP_HOST"]."/{$url}", $data);
};

Framework\Test::add(
    function() use ($post)
    {
        $html = $post(
            "register.html",
            array(
                "first" => "Hello",
                "last" => "World",
                "email" => "info@example.com",
                "password" => "password",
                "register" => "register"
            )
        );
        
        return (stristr($html, "Your account has been created!"));
    },
    "Register form creates user",
    "Functions/Users"
);

Framework\Test::add(
    function() use ($post)
    {
        $html = $post(
            "login.html",
            array(
                "email" => "info@example.com",
                "password" => "password",
                "login" => "login"
            )
        );
        
        return (stristr($html, "Location: /profile.html"));
    },
    "Login form creates user session + redirect to profile",
    "Functions/Users"
);