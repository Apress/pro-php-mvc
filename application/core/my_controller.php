<?php

class MY_Controller extends CI_Controller
{
    public $user;
        
    public static function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }
    
    protected function _isSecure()
    {
        // get user session
        $this->_getUser();
        
        if (!$this->user)
        {
            self::redirect("/login");
        }
    }
    
    protected function _getUser()
    {
        // load session library
        $this->load->library("session");
        
        // get user id
        $id = $this->session->userdata("user");
        
        if ($id)
        {
            // load user model
            $this->load->model("user");
            
            // get user
            $this->user = new User(array(
                "id" => $id
            ));
        }
    }
}