<?php

class Settings {
    use Controller;


// 


    public function index(string $a = '', string $b = '', string $c = ''):void {

        // show(UserMiddleware::getUser()['role']);

        AuthorizationMiddleware::authorize(['rentalservice']);

        if(UserMiddleware::getUser()['role'] == 'rentalservice'){
            

            // show(UserMiddleware::getUser());

            $this->view('rental/settings');
            // echo "rental service";
        }else{
            $this->view('customer/settings');
        }




    }

}