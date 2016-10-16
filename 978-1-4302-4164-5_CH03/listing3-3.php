<?php

class Database
{
    protected $_instance; 

    public function getInstance()
    {
        throw new Exception("Instance is protected");
    } 

    public function setInstance($instance)
    {
        if ($instance instanceof MySQLi)
        {
            $this->_instance = $instance;
        }

        throw new Exception("Instance must be of type MySQLi");
    } 

    public function __construct($host, $username, $password, $schema)
    {
        $this->_instance = new MySQLi($host, $username, $password, $schema);
    } 

    public function query($sql)
    {
        return $this->_instance->query($sql);
    }
}
