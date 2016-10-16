<?php

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    protected $_email = "testing_user";
    protected $_password = "testing_password";

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testRegisterHasFields()
    {
        $this->dispatch("/users/register");
        $this->assertQuery("input[name='first']", "first name field not found");
        $this->assertQuery("input[name='last']", "last name field not found");
        $this->assertQuery("input[name='email']", "email field not found");
        $this->assertQuery("input[name='password']", "password field not found");
        $this->assertQuery("input[name='photo']", "photo field not found");
        $this->assertQuery("input[name='save']", "register button not found");
    }

    public function testRegisterWorks()
    {
        $this->request->setMethod("POST")
            ->setPost(array(
                "first" => "testing",
                "last" => "testing",
                "email" => $this->_email,
                "password" => $this->_password,
                "save" => "register"
            ));

        $this->dispatch("/users/register");
        $this->assertQueryContentContains("p", "account has been created");
    }

    public function testLoginHasFields()
    {
        $this->dispatch("/users/login");
        $this->assertQuery("input[name='email']", "email field not found");
        $this->assertQuery("input[name='password']", "password field not found");
        $this->assertQuery("input[name='login']", "login button not found");
    }

    public function testLoginWorks()
    {
        $this->request
            ->setMethod("POST")
            ->setPost(array(
                "email" => $this->_email,
                "password" => $this->_password,
                "login" => "login"
            ));

        $this->dispatch("/users/login");
        print_r($this->response->getBody());
        $this->assertRedirect($message = "not redirected");
    }


}

