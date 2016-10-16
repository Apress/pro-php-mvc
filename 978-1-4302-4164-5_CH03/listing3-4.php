<?php

class Car
{
    protected $_color;
    protected $_model;

    public function getColor()
    {
        return $this->_color;
    }

    public function setColor($color)
    {
        $this->_color = $color;
        return $this;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }
}

$car = new Car();
$car->setColor("blue");
$car->setModel("b-class");