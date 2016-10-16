<?php

use Shared\Controller as Controller; 
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Users extends Controller
{
    public function register()
    {
        $view = $this->getActionView();
        $view->set("errors", array());

        if (RequestMethods::post("register"))
        {
            $user = new User(array(
                "first" => RequestMethods::post("first"),
                "last" => RequestMethods::post("last"),
                "email" => RequestMethods::post("email"),
                "password" => RequestMethods::post("password")
            ));
            
            if ($user->validate())
            {
                $user->save();
                $this->_upload("photo", $user->id);
                $view->set("success", true);
            }
            
            $view->set("errors", $user->getErrors());
        }
    }

    public function login()
    {
        if (RequestMethods::post("login"))
        {
            $email = RequestMethods::post("email");
            $password = RequestMethods::post("password");
            
            $view = $this->getActionView();
            $error = false;
            
            if (empty($email))
            {
                $view->set("email_error", "Email not provided");
                $error = true;
            }
            
            if (empty($password))
            {
               $view->set("password_error", "Password not provided");
               $error = true;
            }
            
            if (!$error)
            {
                $user = User::first(array(
                    "email = ?" => $email,
                    "password = ?" => $password,
                    "live = ?" => true,
                    "deleted = ?" => false
                ));
                
                if (!empty($user))
                {
                    $this->user = $user;
                    self::redirect("/profile.html");
                }
                else
                {
                    $view->set("password_error", "Email address and/or password are incorrect");
                } 
            }
        }
    }

    public function profile()
    {
        $session = Registry::get("session");
        $user = $this->user;
        
        if (empty($user))
        {
            $user = new StdClass();
            $user->first = "Mr.";
            $user->last = "Smith";
            $user->file = "";
        }
        
        $this->getActionView()->set("user", $user);
    }

    public function search()
    {
        $view = $this->getActionView();
        
        $query = RequestMethods::post("query");
        $order = RequestMethods::post("order", "modified");
        $direction = RequestMethods::post("direction", "desc");
        $page = RequestMethods::post("page", 1);
        $limit = RequestMethods::post("limit", 10);
        
        $count = 0;
        $users = false;
        
        if (RequestMethods::post("search"))
        {
            $where = array(
                "SOUNDEX(first) = SOUNDEX(?)" => $query,
                "live = ?" => true,
                "deleted = ?" => false
            );
            
            $fields = array(
                "id", "first", "last"
            );
            
            $count = User::count($where);
            $users = User::all($where, $fields, $order, $direction, $limit, $page);
        }
        
        $view
            ->set("query", $query)
            ->set("order", $order)
            ->set("direction", $direction)
            ->set("page", $page)
            ->set("limit", $limit)
            ->set("count", $count)
            ->set("users", $users);
    }

    /**
    * @before _secure
    */
    public function settings()
    {
        $view = $this->getActionView();
        $user = $this->getUser();

        if (RequestMethods::post("update"))
        {
            $user = new User(array(
                "first" => RequestMethods::post("first", $user->first),
                "last" => RequestMethods::post("last", $user->last),
                "email" => RequestMethods::post("email", $user->email),
                "password" => RequestMethods::post("password", $user->password)
            ));
            
            if ($user->validate())
            {
                $user->save();
                $this->user = $user;
                $this->_upload("photo", $this->user->id);
                $view->set("success", true);
            }
            
            $view->set("errors", $user->getErrors());
        }
    }

    public function logout()
    {
        $this->setUser(false);
        self::redirect("/users/login.html");
    }

    /**
    * @before _secure
    */
    public function friend($id)
    {
        $user = $this->getUser();
        
        $friend = new Friend(array(
            "user" => $user->id,
            "friend" => $id
        ));
        
        $friend->save();
        
        header("Location: /search.html");
        exit();
    }

    /**
    * @before _secure
    */
    public function unfriend($id)
    {
        $user = $this->getUser();
        
        $friend = Friend::first(array(
            "user" => $user->id,
            "friend" => $id
        ));
        
        if ($friend)
        {
            $friend = new Friend(array(
                "id" => $friend->id
            ));
            $friend->delete();
        }
        
        header("Location: /search.html");
        exit();
    }

    protected function _upload($name, $user)
    {
        if (isset($_FILES[$name]))
        {
            $file = $_FILES[$name];
            $path = APP_PATH."/public/uploads/";

            $time = time();
            $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
            $filename = "{$user}-{$time}.{$extension}";
            
            if (move_uploaded_file($file["tmp_name"], $path.$filename))
            {
                $meta = getimagesize($path.$filename);
                
                if ($meta)
                {
                    $width = $meta[0];
                    $height = $meta[1];
                    
                    $file = new File(array(
                        "name" => $filename,
                        "mime" => $file["type"],
                        "size" => $file["size"],
                        "width" => $width,
                        "height" => $height,
                        "user" => $user
                    ));
                    $file->save();
                }
            }
        }
    }

    /**
    * @before _secure, _admin
    */
    public function edit($id)
    {
        $errors = array();
        
        $user = User::first(array(
            "id = ?" => $id
        ));
        
        if (RequestMethods::post("save"))
        {
            $user->first = RequestMethods::post("first");
            $user->last = RequestMethods::post("last");
            $user->email = RequestMethods::post("email");
            $user->password = RequestMethods::post("password");
            $user->live = (boolean) RequestMethods::post("live");
            $user->admin = (boolean) RequestMethods::post("admin");
            
            if ($user->validate())
            {
                $user->save();
                $this->actionView->set("success", true);
            }
            
            $errors = $user->errors;
        }
        
        $this->actionView
            ->set("user", $user)
            ->set("errors", $errors);
    }
    
    /**
    * @before _secure, _admin
    */
    public function view()
    {
        $this->actionView->set("users", User::all());
    }

    /**
    * @before _secure, _admin
    */
    public function delete($id)
    {
        $user = User::first(array(
            "id = ?" => $id
        ));
        
        if ($user)
        {
            $user->live = false;
            $user->save();
        }
        
        self::redirect("/users/view.html");
    }
    
    /**
    * @before _secure, _admin
    */
    public function undelete($id)
    {
        $user = User::first(array(
            "id = ?" => $id
        ));
        
        if ($user)
        {
            $user->live = true;
            $user->save();
        }
        
        self::redirect("/users/view.html");
    }
}
