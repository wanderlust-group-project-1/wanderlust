<?php

class Orders {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('rental/orders');
    }

    public function list(string $a = '', string $b = '', string $c = ''):void {
        $data = [
            'rental_id' => UserMiddleware::getUser()['id'],
        ];

        // $rent = new RentModel;
        // $orders = $rent->getRentalsByRental($data);

        // $this->view('rental/components/orderlist', ['orders' => $orders]);
    }
}


?>