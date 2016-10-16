<?php

include("_base.php");
include("_exceptions.php");
include("_inspector.php");
include("_methods.php");
include("_database.php");
include("_registry.php");
include("model.php");

class User extends Framework\Model
{
    /**
    * @column
    * @readwrite
    * @primary
    * @type autonumber
    */
    protected $_id;
    
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
    
    /**
    * @column
    * @readwrite
    * @type text
    */
    protected $_notes;
    
    /**
    * @column
    * @readwrite
    * @type boolean
    * @index
    */
    protected $_live;
    
    /**
    * @column
    * @readwrite
    * @type boolean
    * @index
    */
    protected $_deleted;
    
    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_created;
    
    /**
    * @column
    * @readwrite
    * @type datetime
    */
    protected $_modified;
}
