<?php

include("_base.php");
include("_exceptions.php");
include("_inspector.php");
include("_methods.php");
include("cache.php");

function getFriends()
{
    $cache = new Framework\Cache(array(
        "type" => "memcached"
    ));
    $cache = $cache->initialize()->connect();

    $friends = unserialize($cache->get("friends"));
    if (empty($friends))
    {
        // get $friends from the database
        
        $cache->set("friends", serialize($friends));
    }
    
    return $friends;
} 

getFriends();