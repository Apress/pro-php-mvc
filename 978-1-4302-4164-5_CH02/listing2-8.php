<?php 

class Dog
{
    public $doghouse; 

    public function goSleep()
    {
        $location = $this->doghouse->location; 
        $smell = $this->doghouse->smell; 

        echo "The doghouse is at {$location} and smells {$smell}.";
    }
}

class Doghouse
{
    public $location; 
    public $smell; 
}

$doghouse = new Doghouse();
$doghouse->location = "back yard";
$doghouse->smell = "bad"; 

$dog = new Dog();
$dog->doghouse = $doghouse; 
$dog->goSleep();