<?php

class Logout {
    use Controller;

    public function index(){

       $data['email'] = empty($_SESSION['USER']) ? '' : $_SESSION['USER']->email;
        
        session_destroy();
        redirect('home');
    //    $this->view('home',$data);

     
    }
}