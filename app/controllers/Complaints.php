<?php

class Complaints{
    use Controller;

    public function index(){
        
        $this->view('rental/complaints');
    }
}