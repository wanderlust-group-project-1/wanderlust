<?php

class verifiedMiddleware{
    static function run_middleware($controller, $method, $user){
        
        if ($user && $user->is_verified == 0) {
          redirect('verify/resend');
        }
        return $controller;
    }
}