<?php

class User extends Shared\Model
{
    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    */
    protected $_first;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    */
    protected $_last;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    * @index
    */
    protected $_email;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 100
    * @index
    */
    protected $_password;
}
