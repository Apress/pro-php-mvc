<?php

class User extends CI_Model
{
    public $id;
    public $live = true;
    public $deleted = false;
    public $created;
    public $modified;
    public $first;
    public $last;
    public $email;
    public $password;
    
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
        // be a good subclass
        parent::__construct();
        
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
            $query = $this->db
                ->where("id", $this->id)
                ->get("user", 1, 0);
                
            if ($row = $query->row())
            {
                $this->id = (bool) $row->id;
                $this->live = (boolean) $row->live;
                $this->deleted = (boolean) $row->deleted;
                $this->created = $row->created;
                $this->modified = $row->modified;
                $this->first = $row->first;
                $this->last = $row->last;
                $this->email = $row->email;
                $this->password = $row->password;
            }
        }
    }
    
    public function save()
    {
        // initialize data
        $data = array(
            "live" => (int) $this->live,
            "deleted" => (int) $this->deleted,
            "modified" => date("Y-m-d H:i:s"),
            "first" => $this->first,
            "last" => $this->last,
            "email" => $this->email,
            "password" => $this->password
        );
        
        // update
        if ($this->id)
        {
            $where = array("id" => $this->id);
            return $this->db->update("user", $data, $where);
        }
        
        // insert
        $data["created"] = date("Y-m-d H:i:s");
        $this->id = $this->db->insert("user", $data);
        
        // return insert id
        return $this->id;
    }
    
    public static function first($where)
    {
        $user = new User();
        
        // get the first user
        $user->db->where($where);
        $user->db->limit(1);
        
        $query = $user->db->get("user");
        
        // initialze the data
        $data = $query->row();
        $user->_populate($data);
        
        // return the user
        return $user;
    }
    
    public static function count($where)
    {
        $user = new User();
        $user->db->where($where);
        return $user->db->count_all_results("user");
    }
    
    public static function all($where = null, $fields = null, $order = null, $direction = "asc", $limit = null, $page = null)
    {
        $user = new User();
        
        // select fields
        if ($fields)
        {
            $user->db->select(join(",", $fields));
        }
        
        // narrow search
        if ($where)
        {
            $user->db->where($where);
        }
        
        // order results
        if ($order)
        {
            $user->db->order_by($order, $direction);
        }
    
        // limit results
        if ($limit && $page)
        {
            $offset = ($page - 1) * $limit;
            $user->db->limit($limit, $offset);
        }
        
        // get the users
        $query = $user->db->get("user");
        $users = array();
        
        foreach ($query->result() as $row)
        {
            // create + populate user
            $user = new User();
            $user->_populate($row);
            
            // add user to pile
            array_push($users, $user);
        }
        
        return $users;
    }
}