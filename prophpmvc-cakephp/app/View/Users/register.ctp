<h1>Register</h1>
<?php
    echo $this->Form->create("User", array("type" => "file"));
    
    echo $this->Form->input("first");
    echo $this->Form->input("last");
    echo $this->Form->input("email");
    echo $this->Form->input("password", array(
        "type" => "password"
    ));
    echo $this->Form->input("photo", array(
        "type" => "file"
    ));    

    echo $this->Form->end("register");
?>