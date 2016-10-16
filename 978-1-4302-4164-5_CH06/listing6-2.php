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

    private static $_instance; 

    private function __construct()
    {
        // do nothing
    } 

    private function __clone()
    {
        // do nothing
    } 

    public function instance()
    {
        if (!isset(self::$_instance)) 
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

class Car
{
    public $color;
    public $producer;
}