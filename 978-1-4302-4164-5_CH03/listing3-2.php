<?php

class Database
{
    public $instance;

    public function __construct($host, $username, $password, $schema)
    {
        $this->instance = new MySQLi($host, $username, $password, $schema);
    } 

    public function query($sql)
    {
        return $this->instance->query($sql);
    }
}

// you should specify a username, password and schema that matches your database
$database = new Database("localhost", "username", "password", "schema");

$database->instance = "cheese";

$database->query("select * from pantry");