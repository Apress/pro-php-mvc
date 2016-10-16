<?php

class Car
{
    public function __call($name, $arguments)
    {
        echo "hello world!";
    }
}

$car = new Car();
$car->hello();