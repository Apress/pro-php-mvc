<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        /*
        
        $db = Zend_Db::factory(
            "pdo_mysql",
            array(
                "host" => "localhost",
                "username" => "root",
                "password" => "root",
                "dbname" => "prophpmvc-zendframework"
            )
        );
        
        $table = new Zend_Db_Table(array(
            "name" => "user",
            "db" => $db
        ));
        
        $select = $table->select()
            ->where("first LIKE ?", "chris")
            ->order("id ASC");
            
        $user = $table->fetchRow($select);
        
        $id = $table->insert(array(
            "first" => "Chris",
            "last" => "Pitt",
            "email" => "cgpitt@gmail.com",
            "password" => "password",
            "live" => true,
            "deleted" => false,
            "created" => date("Y-m-d"),
            "modified" => date("Y-m-d")
        ));
        
        $user = $table->find($id)->getRow(0);
        
        $user->password .= "123";
        $user->save();
        
        $user->delete();
        
        */
        
        $user = new Application_Model_User(array(
            "first" => "Christopher",
            "last" => "Pitt"
        ));
        
        $id = $user->save();
        
        echo "id: {$id}<br />";
        
        $row = Application_Model_User::first(array("id = ?" => $id));
        
        echo "row ({$id}): ".print_r($row->first, true)."<br />";
        
        $rows = Application_Model_User::all();
        
        echo "rows: ".print_r($rows, true)."<br />";
        
        $count = Application_Model_User::count(array("id = ?" => $id));
        
        echo "count ({$id}): {$count}<br />";
    }
}

