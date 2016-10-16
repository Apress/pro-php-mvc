<?php

use Shared\Controller as Controller; 
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Users extends Controller
{
    public function register()
    {
        if (RequestMethods::post("register"))
        {
            $first = RequestMethods::post("first");
            $last = RequestMethods::post("last");
            $email = RequestMethods::post("email");
            $password = RequestMethods::post("password");
            
            $view = $this->getActionView();
            $error = false;
            
            if (empty($first))
            {
                $view->set("first_error", "First name not provided");
                $error = true;
            }
            
            if (empty($last))
            {
                $view->set("last_error", "Last name not provided");
                $error = true;
            }
            
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
                $user = new User(array(
                    "first" => $first,
                    "last" => $last,
                    "email" => $email,
                    "password" => $password
                ));
                
                $user->save();
                $view->set("success", true);
            }
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
                    $session = Registry::get("session");
                    $session->set("user", serialize($user));
                    
                    header("Location: /users/profile.html");
                    exit();
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
        $user = unserialize($session->get("user", null));
        
        if (empty($user))
        {
            $user = new StdClass();
            $user->first = "Mr.";
            $user->last = "Smith";
        }
        
        $this->getActionView()->set("user", $user);
    }
}
