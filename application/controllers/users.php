<?php

class Users extends CI_Controller
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
    
    protected function _upload($name, $user)
    {
        // load file upload helper
        $this->load->library("upload");
        
        // get extension
        $time = time();
        $path = dirname(BASEPATH)."/uploads/";
        $filename = "{$user}-{$time}";
        
        // do upload
        $this->upload->initialize(array(
            "upload_path" => $path,
            "file_name" => $filename,
            "allowed_types" => "gif|jpg|png"
        ));
        
        if ($this->upload->do_upload($name))
        {
            // get uploaded file data
            $data = $this->upload->data();
            
            // load file model
            $this->load->model("file");
            
            $file = new File(array(
                "name" => $data["file_name"],
                "mime" => $data["file_type"],
                "size" => $data["file_size"],
                "width" => $data["image_width"],
                "height" => $data["image_height"],
                "user" => $user
            ));
            
            $file->save();
        }
    }
    
    public function register()
    {
        $success = false;
        
        // load validation library
        $this->load->library("form_validation");
        
        // if form was posted
        if ($this->input->post("save"))
        {
            // initialize validation rules
            $this->form_validation->set_rules(array(
                array(
                    "field" => "first",
                    "label" => "First",
                    "rules" => "required|alpha|min_length[3]|max_length[32]"
                ),
                array(
                    "field" => "last",
                    "label" => "Last",
                    "rules" => "required|alpha|min_length[3]|max_length[32]"
                ),
                array(
                    "field" => "email",
                    "label" => "Email",
                    "rules" => "required|max_length[100]"
                ),
                array(
                    "field" => "password",
                    "label" => "Password",
                    "rules" => "required|min_length[8]|max_length[32]"
                )
            ));
            
            // if form data passes validation...
            if ($this->form_validation->run())
            {
                // load user model
                $this->load->model("user");
                
                // create new user + save
                $user = new User(array(
                    "first" => $this->input->post("first"),
                    "last" => $this->input->post("last"),
                    "email" => $this->input->post("email"),
                    "password" => $this->input->post("password")
                ));
                $user->save();
                
                // upload file
                $this->_upload("photo", $user->id);
                
                // indicate success in view
                $success = true;
            }
        }
        
        // load view
        $this->load->view("users/register", array(
            "success" => $success
        ));
    }
    
    public function login()
    {
        $errors = null;
        
        // load validation library
        $this->load->library("form_validation");
        
        // if form was posted
        if ($this->input->post("login"))
        {
            // initialize validation rules
            $this->form_validation->set_rules(array(
                array(
                    "field" => "email",
                    "label" => "Email",
                    "rules" => "required|max_length[100]"
                ),
                array(
                    "field" => "password",
                    "label" => "Password",
                    "rules" => "required|min_length[8]|max_length[32]"
                )
            ));
            
            // load user model
            $this->load->model("user");
            
            // create new user + save
            $user = User::first(array(
                "email" => $this->input->post("email"),
                "password" => $this->input->post("password"),
                "live" => 1,
                "deleted" => 0
            ));
            
            // if form data passes validation...
            if ($user && $this->form_validation->run())
            {
                // load session library
                $this->load->library("session");
                
                // save user id to session
                $this->session->set_userdata("user", $user->id);
                
                // redirect to profile page
                self::redirect("/profile");
                
            }
            else
            {
                // indicate errors
                $errors = "Email address and/or password are incorrect";
            }
        }
        
        // load view
        $this->load->view("users/login", array(
            "errors" => $errors
        ));
    }
    
    public function logout()
    {
        // load session library
        $this->load->library("session");
        
        // remove user id
        $this->session->unset_userdata("user");
        
        // redirect to login
        self::redirect("/login");
    }
    
    public function profile()
    {
        // check for user session
        $this->_isSecure();
        
        // get profile photo
        $this->load->model("file");
        
        $file = File::first(array(
            "user" => $this->user->id
        ));
        
        // get thumbnail
        $this->load->library("thumbnail", array(
            "file" => $file
        ));
        
        $filename = $this->thumbnail->getFilename();
        
        // load view
        $this->load->view("users/profile", array(
            "user" => $this->user,
            "filename" => $filename
        ));
    }
    
    public function settings()
    {
        $success = false;
        
        // check for user session
        $this->_isSecure();
        
        // load validation library
        $this->load->library("form_validation");
        
        // if form was posted
        if ($this->input->post("save"))
        {
            // initialize validation rules
            $this->form_validation->set_rules(array(
                array(
                    "field" => "first",
                    "label" => "First",
                    "rules" => "required|alpha|min_length[3]|max_length[32]"
                ),
                array(
                    "field" => "last",
                    "label" => "Last",
                    "rules" => "required|alpha|min_length[3]|max_length[32]"
                ),
                array(
                    "field" => "email",
                    "label" => "Email",
                    "rules" => "required|max_length[100]"
                ),
                array(
                    "field" => "password",
                    "label" => "Password",
                    "rules" => "required|min_length[8]|max_length[32]"
                )
            ));
            
            // if form data passes validation...
            if ($this->form_validation->run())
            {
                // update user
                $this->user->first = $this->input->post("first");
                $this->user->last = $this->input->post("last");
                $this->user->email = $this->input->post("email");
                $this->user->password = $this->input->post("password");
                $this->user->save();
                
                // indicate success in view
                $success = true;
            }
        }
        
        // load view
        $this->load->view("users/settings", array(
            "success" => $success,
            "user" => $this->user
        ));
    }
    
    public function search()
    {
        // get posted data
        $query = $this->input->post("query");
        $order = $this->input->post("order");
        $direction = $this->input->post("direction");
        $page = $this->input->post("page");
        $limit = $this->input->post("limit");
        
        // default null values
        $order = $order ? $order : "modified";
        $direction = $direction ? $direction : "desc";
        $limit = $limit ? $limit : 10;
        $page = $page ? $page : 1;
        $count = 0;
        $users = null;
        
        if ($this->input->post("search"))
        {
            $where = array(
                "first" => $query,
                "live" => 1,
                "deleted" => 0
            );
            
            $fields = array(
                "id", "first", "last"
            );
            
            // load user model
            $this->load->model("user");
            
            // get count + results
            $count = User::count($where);
            $users = User::all(
                $where,
                $fields,
                $order,
                $direction,
                $limit,
                $page
            );
        }
        
        // load view
        $this->load->view("users/search", array(
            "query" => $query,
            "order" => $order,
            "direction" => $direction,
            "page" => $page,
            "limit" => $limit,
            "count" => $count,
            "users" => $users
        ));
    }
}