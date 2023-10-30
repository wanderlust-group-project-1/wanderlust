<?php

class Profile {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {

        $user = $_SESSION['USER'];
        // echo $user->role;

        if ($user->role == 'customer') {
            $this->view('customer/profile');
        } else if ($user->role == 'guide') {
            $this->view('guide/profile');
        } else if ($user->role == 'admin') {
            $this->view('rental-service/profile');
        } 
        
        // else {
        //     $this->view('profile');
        // }

        // $this->view('profile');
    }
}