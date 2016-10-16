<?php

class UsersControllerTest extends ControllerTestCase
{
    public function testRegisterGet()
    {
        $result = $this->testAction("/register", array(
            "method" => "get",
            "return" => "contents"
        ));

        $this->assertContains("data[User][first]", $result);
        $this->assertContains("data[User][last]", $result);
        $this->assertContains("data[User][email]", $result);
        $this->assertContains("data[User][password]", $result);
        $this->assertContains("data[User][photo]", $result);
    }

    public function testLoginGet()
    {
        $result = $this->testAction("/login", array(
            "method" => "get",
            "return" => "contents"
        ));

        $this->assertContains("data[User][email]", $result);
        $this->assertContains("data[User][password]", $result);
    }

    public function testLogoutGet()
    {
        $result = $this->testAction("/logout", array(
            "method" => "get",
            "return" => "contents"
        ));
    }

    public function testProfileGet()
    {
        $result = $this->testAction("/profile", array(
            "method" => "get",
            "return" => "contents"
        ));
    }

    public function testLoginPost()
    {
        $result = $this->testAction("/login", array(
            "method" => "post",
            "return" => "contents",
            "data" => array(
                "User" => array(
                    "email" => "cgpitt@gmail.com",
                    "password" => "chris"
                )
            )
        ));

        $this->assertEquals(true, isset($this->headers["Location"]));

        if (isset($this->headers["Location"]))
        {
            $this->assertContains("/profile", $this->headers["Location"]);
        }
    }
}