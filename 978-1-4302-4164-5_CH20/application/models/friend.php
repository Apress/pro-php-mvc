<?php

class Friend extends Shared\Model
{
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_user;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_friend;
}   
