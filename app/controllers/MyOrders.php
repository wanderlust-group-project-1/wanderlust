<?php

class MyOrders {
    use Controller;

    
    public function index(string $a = '', string $b = '', string $c = ''):void {

        

      
        $this->view('customer/orders');
    }

    public function list(string $a = '', string $b = '', string $c = ''):void {
        $data = [
            'customer_id' => UserMiddleware::getUser()['id'],
        ];

        $rent = new RentModel;
        $orders = $rent->getRentalsByCustomer($data);

        $this->view('customer/components/orderlist', ['orders' => $orders]);
    }

}