<?php

include("listing6-1.php");
include("registry.php");

Framework\Registry::set("ford", new Ford());

$car = new Car();
$car->color = "Blue";
$car->producer = Framework\Registry::get("ford");

echo Framework\Registry::get("ford")->produces($car); 
echo Framework\Registry::get("ford")->founder;
