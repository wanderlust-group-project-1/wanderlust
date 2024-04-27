<?php

class Settings {
    use Controller;


// 


    public function index(string $a = '', string $b = '', string $c = ''):void {

        // show(UserMiddleware::getUser()['role']);

        AuthorizationMiddleware::authorize(['rentalservice','guide','customer']);

        if(UserMiddleware::getUser()['role'] == 'rentalservice'){
            

            $settings = new RentalSettingsModel;
            $data['settings'] = $settings->first(['rentalservice_id' => UserMiddleware::getUser()['id']]);
            // $data

            // show(UserMiddleware::getUser());

            $this->view('rental/settings',$data);
            // echo "rental service";
        }
        
        else if(UserMiddleware::getUser()['role'] == 'guide'){
            $this->view('guide/settings');
        }
        else{
            $this->view('customer/settings');
        }




    }

}