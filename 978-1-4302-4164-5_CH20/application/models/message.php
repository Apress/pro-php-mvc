<?php

class Message extends Shared\Model
{
    /**
    * @column
    * @readwrite
    * @type text
    * @length 256
    *
    * @validate required
    * @label body
    */
    protected $_body;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_message;
    
    /**
    * @column
    * @readwrite
    * @type integer
    */
    protected $_user;

    public function getReplies()
    {
        return self::all(array(
            "message = ?" => $this->getId(),
            "live = ?" => true,
            "deleted = ?" => false 
        ), array(
            "*",
            "(SELECT CONCAT(first, \" \", last) FROM user WHERE user.id = message.user)" => "user_name"
        ), "created", "desc");
    }
    public static function fetchReplies($id)
    {
        $message = new Message(array(
            "id" => $id
        ));
        return $message->getReplies();
    }

}
