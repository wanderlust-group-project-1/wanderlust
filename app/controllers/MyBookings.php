<?php

class MyBookings{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
        AuthorizationMiddleware::authorize(['customer']);
        $this->view('customer/myBookings');
    }
}