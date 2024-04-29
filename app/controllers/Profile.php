<?php

class Profile {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {

        
        AuthorizationMiddleware::authorize(['customer',]);



        
        $user = UserMiddleware::getUser();
        // echo $user->role;
        

        if ($user['role']== 'customer') {

        $rent = new RentModel;
        $guideBooking = new GuideBookingsModel;
        $complaint = new RentComplaintModel;
        $data = [ 
            'rental' => $rent->getUpcomingRentByCustomer(['customer_id' => $user['id']])[0],
            'ordersCount' => $rent->count(['customer_id' => $user['id'], 'status' => 'completed']) + $rent->count(['customer_id' => $user['id'], 'status' => 'rented']),
            'guideBookingsCount' => $guideBooking->count(['customer_id' => $user['id'], 'status' => 'completed']),
            'complaintCount' => sizeof($complaint->getComplaintsByCustomer(['customer_id' => $user['id']]))

        ];

        // show($data);


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