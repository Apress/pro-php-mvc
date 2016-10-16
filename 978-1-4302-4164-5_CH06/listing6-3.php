<?php

include("listing6-2.php");

$ford = Ford::instance();

$car = new Car();
$car->color = "Blue";
$car->producer = $ford;

echo $ford->produces($car);
echo $ford->founder; 