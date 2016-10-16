<?php

class File extends CI_Model
{
    public $id;
    public $live = true;
    public $deleted = false;
    public $created;
    public $modified;
    public $name;
    public $mime;
    public $size;
    public $width;
    public $height;
    public $user;
    
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
                ->get("file", 1, 0);
                
            if ($row = $query->row())
            {
                $this->id = (bool) $row->id;
                $this->live = (boolean) $row->live;
                $this->deleted = (boolean) $row->deleted;
                $this->created = $row->created;
                $this->modified = $row->modified;
                $this->name = $row->name;
                $this->mime = $row->mime;
                $this->size = $row->size;
                $this->width = $row->width;
                $this->height = $row->height;
                $this->user = $row->user;
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
            "name" => $this->name,
            "mime" => $this->mime,
            "size" => $this->size,
            "width" => $this->width,
            "height" => $this->height,
            "user" => $this->user
        );
        
        // update
        if ($this->id)
        {
            $where = array("id" => $this->id);
            return $this->db->update("file", $data, $where);
        }
        
        // insert
        $data["created"] = date("Y-m-d H:i:s");
        $this->id = $this->db->insert("file", $data);
        
        // return insert id
        return $this->id;
    }
    
    public static function first($where)
    {
        $user = new File();
        
        // get the first user
        $user->db->where($where);
        $user->db->limit(1);
        
        $query = $user->db->get("file");
        
        // initialze the data
        $data = $query->row();
        $user->_populate($data);
        
        // return the user
        return $user;
    }
}