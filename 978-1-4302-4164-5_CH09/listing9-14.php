<?php

include("_base.php");
include("_exceptions.php");
include("_inspector.php");
include("_methods.php");
include("database.php");

$database = new Framework\Database(array(
    "type" => "mysql",
    "options" => array(
        "host" => "localhost",
        "username" => "prophpmvc",
        "password" => "prophpmvc",
        "schema" => "prophpmvc",
        "port" => "3306"
    )
));
$database = $database->initialize()->connect();

$all = $database->query()
    ->from("users", array(
        "first_name",
        "last_name" => "surname"
    ))
    ->join("points", "points.id = users.id", array(
        "points" => "rewards"
    ))
    ->where("first_name = ?", "chris")
    ->order("last_name", "desc")
    ->limit(100)
    ->all();

$print = print_r($all, true);
echo "all => {$print}";

$id = $database->query()
    ->from("users")
    ->save(array(
       "first_name" => "Liz",
       "last_name" => "Pitt" 
    ));

echo "id => {$id}\n";

$affected = $database->query()
    ->from("users")
    ->where("first_name = ?", "Liz")
    ->delete();
    
echo "affected => {$affected}\n";

$id = $database->query()
    ->from("users")
    ->where("first_name = ?", "Chris")
    ->save(array(
       "modified" => date("Y-m-d H:i:s")
    ));

echo "id => {$id}\n";

$count = $database->query()
    ->from("users")
    ->count();

echo "count => {$count}\n";
