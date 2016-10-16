<?php

use Shared\Controller as Controller;
use Framework\RequestMethods as RequestMethods;

class Messages extends Controller
{
    public function add()
    {
        $user = $this->getUser();
        
        if (RequestMethods::post("share"))
        {
            $message = new Message(array(
                "body" => RequestMethods::post("body"),
                "message" => RequestMethods::post("message"),
                "user" => $user->id
            ));
            
            if ($message->validate())
            {
                $message->save();
                header("Location: /");
                exit();
            }
        }
    }
} 
