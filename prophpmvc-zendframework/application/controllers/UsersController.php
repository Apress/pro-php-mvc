<?php

class UsersController extends Zend_Controller_Action
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
        // initialize session
        $session = new Zend_Session_Namespace("application");
        
        // get user id
        $id = $session->user;
        
        if ($id)
        {
            // get user
            $this->user = Application_Model_User::first(array("id = ?" => $id));
        }
    }

    protected function _upload($name, $user)
    {
        // get extension
        $time = time();
        $path = dirname(APPLICATION_PATH)."/public/uploads/";
        $filename = "{$user}-{$time}";

        // instantiate file transfer class
        $adapter = new Zend_File_Transfer_Adapter_Http(array(
            "useByteString" => false
        ));
        $adapter->setDestination($path);
        
        if ($adapter->receive())
        {
            // get file data
            $size = $adapter->getFileSize($name);
            $file = $adapter->getFileName($name);

            // add extension to filename
            $parts = explode(".", $file);
            $filename .= ".".end($parts);

            if (rename($file, "{$path}/{$filename}"))
            {
                // get image size/mime
                $dimensions = getimagesize("{$path}/{$filename}");

                // create new file + save
                $file = new Application_Model_File(array(
                    "name" => $filename,
                    "mime" => $dimensions["mime"],
                    "size" => $size,
                    "width" => $dimensions[0],
                    "height" => $dimensions[1],
                    "user" => $user
                ));
                
                $file->save();
            }
        }
    }

    protected function _thumbnail($file)
    {
        $path = dirname(APPLICATION_PATH)."/public/uploads";
        
        $width = 64;
        $height = 64;
        
        $name = $file->name;
        $filename = pathinfo($name, PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        
        if ($filename && $extension)
        {
            $thumbnail = "{$filename}-{$width}x{$height}.{$extension}";
            
            if (!file_exists("{$path}/{$thumbnail}"))
            { 
                $imagine = new Imagine\Gd\Imagine();
                
                $size = new Imagine\Image\Box($width, $height);
                $mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                
                $imagine
                    ->open("{$path}/{$name}")
                    ->thumbnail($size, $mode)
                    ->save("{$path}/{$thumbnail}");
            }
            
            return $thumbnail;
        }

    }

    public function registerAction()
    {
        $valid = true;
        $errors = array();
        $success = false;

        $first = $this->_request->getPost("first");
        $last = $this->_request->getPost("last");
        $email = $this->_request->getPost("email");
        $password = $this->_request->getPost("password");
        
        // if form was posted
        if ($this->_request->getPost("save"))
        {
            // enforce validation rules

            $alnum = new Zend_Validate_Alnum();
            $stringLength1 = new Zend_Validate_StringLength(array("min" => 3, "max" => 32));
            $stringLength2 = new Zend_Validate_StringLength(array("min" => 0, "max" => 255));

            if (empty($first) || !$alnum->isValid($first) || !$stringLength1->isValid($first))
            {
                $valid = false;
                $errors["first"] = "First field required";
            }

            if (empty($last) || !$alnum->isValid($last) || !$stringLength1->isValid($last))
            {
                $valid = false;
                $errors["last"] = "Last field required";
            }

            if (empty($email) || !$stringLength2->isValid($email))
            {
                $valid = false;
                $errors["email"] = "Email field required";
            }

            if (empty($password) || !$stringLength2->isValid($password))
            {
                $valid = false;
                $errors["password"] = "Password field required";
            }
            
            // if form data passes validation...
            if ($valid)
            {
                // create new user + save
                $user = new Application_Model_User(array(
                    "first" => $first,
                    "last" => $last,
                    "email" => $email,
                    "password" => $password
                ));
                $id = $user->save();

                $this->_upload("photo", $id);
                
                // indicate success in view
                $success = true;
            }
        }
        
        // load view
        $this->view->errors = $errors;
        $this->view->success = $success;
    }

    public function loginAction()
    {
        $valid = true;
        $errors = array();

        $email = $this->_request->getPost("email");
        $password = $this->_request->getPost("password");
        
        // if form was posted
        if ($this->_request->getPost("login"))
        {
            // enforce validation rules

            $alnum = new Zend_Validate_Alnum();
            $stringLength = new Zend_Validate_StringLength(array("min" => 0, "max" => 255));

            if (empty($email) || !$stringLength->isValid($email))
            {
                $valid = false;
                $errors["email"] = "Email field required";
            }

            if (empty($password) || !$stringLength->isValid($password))
            {
                $valid = false;
                $errors["password"] = "Password field required";
            }
            
            // if form data passes validation...
            if ($valid)
            {
                $user = Application_Model_User::first(array(
                    "email = ?" => $email,
                    "password = ?" => $password
                ));

                if ($user)
                {
                    // initialize session
                    $session = new Zend_Session_Namespace("application");
                    
                    // save user id to session
                    $session->user = $user->id;
                    
                    // redirect to profile page
                    $this->_helper->redirector->gotoRoute(array(
                       "controller" => "users",
                       "action" => "profile"
                    ));
                    
                }
                else
                {
                    // indicate errors
                    $errors["password"] = "Email address and/or password are incorrect";
                }
            }
        }
        
        // load view
        $this->view->errors = $errors;
    }

    public function logoutAction()
    {
        // unlock session
        $session = new Zend_Session_Namespace("application");
        $session->unlock();

        // kill session
        Zend_Session::namespaceUnset("application");
        
        // redirect to login
        self::redirect("/login");
    }
    
    public function profileAction()
    {
        // check for user session
        $this->_isSecure();

        // load thumbnail
        $file = Application_Model_File::first(array(
            "user = ?" => $this->user->id
        ));
        
        // load view
        $this->view->user = $this->user;

        if ($file)
        {
            $filename = $this->_thumbnail($file);
            $this->view->filename = $filename;
        }
    }

    public function settingsAction()
    {
        // check for user session
        $this->_isSecure();

        $valid = true;
        $errors = array();
        $success = false;

        $first = $this->_request->getPost("first");
        $last = $this->_request->getPost("last");
        $email = $this->_request->getPost("email");
        $password = $this->_request->getPost("password");
        
        // if form was posted
        if ($this->_request->getPost("save"))
        {
            // enforce validation rules

            $alnum = new Zend_Validate_Alnum();
            $stringLength1 = new Zend_Validate_StringLength(array("min" => 3, "max" => 32));
            $stringLength2 = new Zend_Validate_StringLength(array("min" => 0, "max" => 255));

            if (empty($first) || !$alnum->isValid($first) || !$stringLength1->isValid($first))
            {
                $valid = false;
                $errors["first"] = "First field required";
            }

            if (empty($last) || !$alnum->isValid($last) || !$stringLength1->isValid($last))
            {
                $valid = false;
                $errors["last"] = "Last field required";
            }

            if (empty($email) || !$stringLength2->isValid($email))
            {
                $valid = false;
                $errors["email"] = "Email field required";
            }

            if (empty($password) || !$stringLength2->isValid($password))
            {
                $valid = false;
                $errors["password"] = "Password field required";
            }
            
            // if form data passes validation...
            if ($valid)
            {
                // save changes to user
                $this->user->first = $first;
                $this->user->last = $last;
                $this->user->email = $email;
                $this->user->password = $password;
                $this->user->save();
                
                // indicate success in view
                $success = true;
            }
        }
        
        // load view
        $this->view->errors = $errors;
        $this->view->success = $success;
        $this->view->user = $this->user;
    }
    
    public function searchAction()
    {
        // get posted data
        $query = $this->_request->getPost("query");
        $order = $this->_request->getPost("order");
        $direction = $this->_request->getPost("direction");
        $page = $this->_request->getPost("page");
        $limit = $this->_request->getPost("limit");
        
        // default null values
        $order = $order ? $order : "modified";
        $direction = $direction ? $direction : "desc";
        $limit = $limit ? $limit : 10;
        $page = $page ? $page : 1;
        $count = 0;
        $users = null;
        
        if ($this->_request->getPost("search"))
        {
            $where = array(
                "first = ?" => $query,
                "live = ?" => 1,
                "deleted = ?" => 0
            );
            
            $fields = array(
                "id", "first", "last"
            );
            
            // get count + results
            $count = Application_Model_User::count($where);
            $users = Application_Model_User::all(
                $where,
                $fields,
                $order,
                $direction,
                $limit,
                $page
            );
        }
        
        // load view
        $this->view->query = $query;
        $this->view->order = $order;
        $this->view->direction = $direction;
        $this->view->page = $page;
        $this->view->limit = $limit;
        $this->view->count = $count;
        $this->view->users = $users;
    }
}

