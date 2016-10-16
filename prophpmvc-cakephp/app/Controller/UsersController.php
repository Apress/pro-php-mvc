<?php

App::uses("AppController", "Controller");

class UsersController extends AppController
{
    public $name = "Users";

    public $helpers = array("Html", "Form");

    public $uses = array("User", "Photo");

    public $user;

    protected function _isSecure()
    {
        $this->_getUser();
        
        if (!$this->user)
        {
            $this->redirect(array(
                "action" => "login"
            ));
        }
    }
    
    protected function _getUser()
    {
        $id = $this->Session->read("user");
        
        if ($id)
        {
            $this->user = $this->User->findById($id);
        }
    }

    protected function _upload($name, $user)
    {
        App::uses("File", "Utility");

        $meta = $this->request->data["User"][$name];
        $file = new File($meta["tmp_name"], false);

        if ($file)
        {
            $path = WWW_ROOT."uploads";

            if ($file->copy($path.DS.$meta["name"]))
            {
                $info = $file->info();
                $size = getimagesize($meta["tmp_name"]);

                $this->Photo->save(array(
                    "user_id" => $user,
                    "mime" => $info["mime"],
                    "size" => $info["filesize"],
                    "width" => $size[0],
                    "height" => $size[1],
                    "name" => $meta["name"]
                ));
            }
        }
    }

    protected function _thumbnail($photo)
    {
        App::uses("File", "Utility");

        App::import("Vendor", "Imagine/Image/ManipulatorInterface");
        App::import("Vendor", "Imagine/Image/ImageInterface");
        App::import("Vendor", "Imagine/Image/ImagineInterface");
        App::import("Vendor", "Imagine/Image/BoxInterface");
        App::import("Vendor", "Imagine/Image/PointInterface");
        App::import("Vendor", "Imagine/Image/Point");
        App::import("Vendor", "Imagine/Gd/Image");
        App::import("Vendor", "Imagine/Gd/Imagine");
        App::import("Vendor", "Imagine/Image/Box");

        $path = WWW_ROOT."uploads";
        
        $width = 64;
        $height = 64;
        
        $file = new File($path.DS.$photo["name"]);
        
        if ($file)
        {
            $name = $file->name();
            $extension = $file->ext();

            $thumbnail = "{$name}-{$width}x{$height}.{$extension}";
            
            if (!file_exists("{$path}/{$thumbnail}"))
            { 
                $imagine = new Imagine\Gd\Imagine();
                
                $size = new Imagine\Image\Box($width, $height);
                $mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                
                $imagine
                    ->open("{$path}/{$name}.{$extension}")
                    ->thumbnail($size, $mode)
                    ->save("{$path}/{$thumbnail}");
            }
            
            return $thumbnail;
        }
    }

    public function login()
    {
        if ($this->request->is("post"))
        {
            $email = $this->request->data["User"]["email"];
            $password = $this->request->data["User"]["password"];

            $user = $this->User->findByEmailAndPassword($email, $password);

            if ($user)
            {
                $this->Session->write("user", $user["User"]["id"]);

                $this->redirect(array(
                    "action" => "profile"
                ));
            }
            else
            {
                $this->Session->setFlash("Logn details invalid.");
            }
        }
    }

    public function logout()
    {
        $this->Session->delete("user");

        $this->redirect(array(
            "action" => "login"
        ));
    }
    
    public function profile()
    {
        $this->_isSecure();

        if (sizeof($this->user["Photo"]))
        {
            $this->set("photo", $this->_thumbnail($this->user["Photo"][0]));
        }

        $this->set("user", $this->user);
    }

    public function register()
    {
        if ($this->request->is("post"))
        {
            if ($this->User->save($this->request->data))
            {
                $this->_upload("photo", $this->User->id);
                $this->Session->setFlash("Your account has been created.");
            }
            else
            {
                $this->Session->setFlash("An error occurred while creating your account.");
            }
        }
    }

    public function settings()
    {
        $this->_isSecure();   
        $this->set("user", $this->user);

        $this->User->id = $this->user["User"]["id"];

        if ($this->request->is("get"))
        {
            $this->request->data = $this->user;
        }
        else if ($this->request->is("post"))
        {
            if ($this->User->save($this->request->data))
            {
                $this->_upload("photo", $this->User->id);
                $this->Session->setFlash("Your account has been updated.");
            }
            else
            {
                $this->Session->setFlash("An error occurred while updating your account.");
            }
        }
    }

    public function search()
    {
        $data = $this->request->data;

        if (isset($data["Search"]))
        {
            $query = !empty($data["Search"]["query"]) ? $data["Search"]["query"] : "";
            $order = !empty($data["Search"]["order"]) ? $data["Search"]["order"] : "modified";
            $direction = !empty($data["Search"]["direction"]) ? $data["Search"]["direction"] : "desc";
            $page = !empty($data["Search"]["page"]) ? $data["Search"]["page"] : 1;
            $limit = !empty($data["Search"]["limit"]) ? $data["Search"]["limit"] : 10;
        }

        $users = null;
        
        if ($this->request->is("post"))
        {
            $conditions = array(
                "conditions" => array(
                    "first = ?" => $query
                ),
                "fields" => array(
                    "id", "first", "last"
                ),
                "order" => array(
                    $order . " " . $direction
                ),
                "page" => $limit,
                "offset" => ($page - 1) * $limit
            );

            $users = $this->User->find("all", $conditions);
            $count = $this->User->find("count", $conditions);
        }
        
        $this->set("query", $query);
        $this->set("order", $order);
        $this->set("direction", $direction);
        $this->set("page", $page);
        $this->set("limit", $limit);
        $this->set("count", $count);
        $this->set("users", $users);
    }
}