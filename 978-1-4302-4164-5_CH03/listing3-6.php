<?php

class Car
{
    protected $_color;
    protected $_model;

    public function __call($name, $arguments)
    {
        $first = isset($arguments[0]) ? $arguments[0] : null;

        switch ($name)
        {
            case "getColor":
                return $this->_color; 

            case "setColor":
                $this->_color = $first; 
                return $this; 

            case "getModel":
                return $this->_model; 

            case "setModel":
                $this->_model = $first; 
                return $this;
        }
    }
}

$car = new Car();
$car->setColor("blue")->setModel("b-class");
echo $car->getModel();