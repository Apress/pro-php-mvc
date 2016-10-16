<h1>Login</h1>
<?php
    echo $this->Form->create("User");
    
    echo $this->Form->input("email");
    echo $this->Form->input("password", array(
        "type" => "password"
    ));

    echo $this->Form->end("login");
?>