<?php

class Application_Model_User
{
    protected $_table;
    
    public $id;
    public $first;
    public $last;
    public $email;
    public $password;
    public $live = true;
    public $deleted = false;
    public $created;
    public $modified;
    
    public function setTable($table)
    {
        // if string given, make new table class
        if (is_string($table))
        {
            $table = new $table();
        }
        
        // ...else if object given that is not a valid table class...
        if (!$table instanceof Zend_Db_Table_Abstract)
        {
            throw new Exception("Invalid table specified");
        }
        
        $this->_table = $table;
        return $this;
    }
    
    public function getTable()
    {
        if (!$this->_table)
        {
            // define the DbTable fir first time it is needed
            $this->setTable("Application_Model_DbTable_User");
        }
        
        return $this->_table;
    }
    
    protected function _populate($options)
    {
        // set all existing properties
        foreach ($options as $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->$key = $value;
            }
        }
    }
    
    public function __construct($options = array())
    {
        // populate values
        if (sizeof($options))
        {
            $this->_populate($options);
        }
        
        // load row
        $this->load();
    }
    
    public function load()
    {
        if ($this->id)
        {
            $row = $this->first(array("id = ?" => $this->id));
            
            if (!$row)
            {
                return;
            }
                
            $this->id = $row->id;
            $this->first = $row->first;
            $this->last = $row->last;
            $this->email = $row->email;
            $this->password = $row->password;
            $this->live = (boolean) $row->live;
            $this->deleted = (boolean) $row->deleted;
            $this->created = $row->created;
            $this->modified = $row->modified;
        }
    }
    
    public function save()
    {
        // initialize data
        $data = array(
            "first" => $this->first,
            "last" => $this->last,
            "email" => $this->email,
            "password" => $this->password,
            "live" => (int) $this->live,
            "deleted" => (int) $this->deleted,
            "modified" => date("Y-m-d H:i:s")
        );
        
        if ($this->id)
        {
            // update
            $where = array("id = ?" => $this->id);
            return $this->getTable()->update($data, $where);
        }
        else
        {
            // insert
            $data["created"] = date("Y-m-d H:i:s");
            return $this->getTable()->insert($data);
        }
    }
    
    public static function first($where = null)
    {
        $user = new self();
        $table = $user->getTable();
        $query = $table->select()->limit(1);
        
        if (is_array($where))
        {
            foreach ($where as $key => $value)
            {
                $query->where($key, $value);
            }
        }
        
        if ($row = $table->fetchRow($query))
        {
            $user->_populate($row->toArray());
            return $user;
        }
        
        return null;
    }
    
    public static function count($where = null)
    {
        $user = new self();
        $table = $user->getTable();
        $query = $table->select()->from($table, array("num" => "COUNT(1)"))->limit(1);
        
        if (is_array($where))
        {
            foreach ($where as $key => $value)
            {
                $query->where($key, $value);
            }
        }
        
        $row = $table->fetchRow($query);
        return $row->num;
    }
    
    public static function all($where = null, $fields = null, $order = null, $direction = "asc", $limit = null, $page = null)
    {
        $user = new self();
        $table = $user->getTable();
        $adapter = $table->getAdapter();
        $query = $adapter->select();
        
        // select fields
        if ($fields)
        {
            $query->from("user", $fields);
        }
        
        // narrow search
        if (is_array($where))
        {
            foreach ($where as $key => $value)
            {
                $query->where($key, $value);
            }
        }
        
        // order results
        if ($order)
        {
            $query->order("{$order} {$direction}");
        }
    
        // limit results
        if ($limit && $page)
        {
            $offset = ($page - 1) * $limit;
            $query->limit($limit, $offset);
        }
        
        // get the users
        $users = array();
        $rows = $adapter->fetchAll($query);
        
        foreach ($rows as $row)
        {
            // create + populate user
            $user = new self();
            $user->_populate($row);
            
            // add user to pile
            array_push($users, $user);
        }
        
        return $users;
    }
}