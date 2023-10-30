<?php

class UserMiddleware {

    static function user($user){
        if (!$user || !(isset($user['role']))) {
            return false;
        }
        // if ()

        if ($user['role'] == 'customer') {
            $customer = new CustomerModel;
            $customer = $customer->first([
                'user_id'=> $user['id']
                ]);
            // echo $customer;
            $user = (object) array_merge((array) $user, (array) $customer);
            return $user;
        }
        if ($user['role'] == 'guide') {
            $guide = new GuideModel;
            $guide = $guide->first([
                'user_id'=> $user['id']
            ]);
            // show($d);
            $user = (object) array_merge((array) $user, (array) $guide);
            return $user;
        }
        if ($user['role'] == 'rental_service') {
            $rental_service = new RentalServiceModel;
            $rental_service = $rental_service->first([
                'user_id'=> $user['id']
                ]);
            $user = (object) array_merge((array) $user, (array) $rental_service);
            return $user;
        }
    }
    
}







?>