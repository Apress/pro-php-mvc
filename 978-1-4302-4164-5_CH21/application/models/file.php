<?php

class File extends Shared\Model
{
    /**
    * @column
    * @readwrite
    * @type text
    * @length 255
    */
    protected $_name;
    
    /**
    * @column
    * @readwrite
    * @type text
    * @length 32
    */
    protected $_mime;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_size;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_width;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_height;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_user;
}  
