<?php

class Ford
{
    public $founder = "Henry Ford";
    public $headquarters = "Detroit";
    public $employees = 164000;
    
    public function produces($car)
    {
        return $car->producer == $this;
    }
}

class Car
{
    public $color;
    public $producer;
}

$ford = new Ford();

$car = new Car();
$car->color = "Blue";
$car->producer = $ford;

echo $ford->produces($car);
echo $ford->founder; 