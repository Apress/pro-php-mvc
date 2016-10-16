<?php

include("listing10-1.php");

$database = new Framework\Database(array(
    "type" => "mysql",
    "options" => array(
        "host" => "localhost",
        "username" => "prophpmvc",
        "password" => "prophpmvc",
        "schema" => "prophpmvc"
    )
));
$database = $database->initialize()->connect();

$user = new User(array(
    "connector" => $database
));
$database->sync($user);