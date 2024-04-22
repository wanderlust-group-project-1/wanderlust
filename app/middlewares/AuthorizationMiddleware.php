<?php

class AuthorizationMiddleware {

    public static function authorize(array $allowedRoles):void {
        $user = UserMiddleware::getUser();

        if (!$user || !(isset($user['role']))) {
           redirect('login');
        }

        if (!in_array($user['role'], $allowedRoles)) {
           if ($user['role'] == 'admin') {
               redirect('admin');
           }
           else if ($user['role'] == 'guide' || $user['role'] == 'rentalservice') {
               redirect('dashboard');
           }
           else {
               redirect('home');
           } 
        }


    }
}