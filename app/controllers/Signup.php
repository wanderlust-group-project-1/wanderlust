<?php

class Signup {
    use Controller;

    public function index(){

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $user = new User;

            if($user->validate($_POST)){
                $user->insert($_POST);
                redirect('login');
            }
        }
        

        // show($_POST);
        $data['errors'] = $user->errors;
        $this->view('signup',$data);
    }
}