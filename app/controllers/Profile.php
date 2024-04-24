<?php

class Profile {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {

        
        AuthorizationMiddleware::authorize(['customer',]);



        
        $user = UserMiddleware::getUser();
        // echo $user->role;
        

        if ($user['role']== 'customer') {

        $rent = new RentModel;
        $data = [ 
            'rental' => $rent->getUpcomingRentByCustomer(['customer_id' => $user['id']])[0],
        ];


            $this->view('customer/profile', $data);
        } else if ($user['role'] == 'guide') {
            $this->view('guide/profile');
        } else if ($user['role'] == 'rentalservice') {
            $this->view('rental-service/profile');
        } 
        
        // else {
        //     $this->view('profile');
        // }

        // $this->view('profile');
    }
}