<?php

class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->view('welcome_message');
    }
    
    public function tests()
    {
        $this->load->library("unit_test");
        
        $test = $this->testing();
        $expected_result = "hello world";
        $test_name = "testing function returns hello world";
        $this->unit->run($test, $expected_result, $test_name);
        
        echo $this->unit->report();
    }
    
    public function testing()
    {
        return "hello world";
    }
}