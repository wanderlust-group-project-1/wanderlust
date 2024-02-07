<?php

class UserMiddleware {

    static $user = [];

    public static function getUser(): array {
       return Self::$user;
    }

    static function user($user){
        if (!$user || !(isset($user['role']))) {
            return false;
        }
        // if ()
        // if ($user['is_verified'] == 0) {
        //     return false;
        // }

        if ($user['role'] == 'customer') {
            $customer = new CustomerModel;
            $customer = $customer->first([
                'user_id'=> $user['id']
                ]);
            // echo $customer;
            $user = (object) array_merge((array) $user, (array) $customer);
            Self::$user = (array) $user;

            return $user;
        }
        if ($user['role'] == 'guide') {
            $guide = new GuideModel;
            $guide = $guide->first([
                'user_id'=> $user['id']
            ]);
            // show($d);
            $user = (object) array_merge((array) $user, (array) $guide);
            Self::$user = (array) $user;

            return $user;
        }
        if ($user['role'] == 'rentalservice') {
            $rental_service = new RentalServiceModel;
            $rental_service = $rental_service->first([
                'user_id'=> $user['id']
                ]);
            $user = (object) array_merge((array) $user, (array) $rental_service);
            Self::$user = (array) $user;

            return $user;
        }
        if ($user['role'] == 'admin') {


            Self::$user = (array) $user;
            return (object)$user;


        }
    }
    
}
