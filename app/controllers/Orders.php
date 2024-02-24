<?php

class Orders {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('rental/orders');
    }

    public function list(string $a = '', string $b = '', string $c = ''):void {
        $data = [
            'rentalservice_id' => UserMiddleware::getUser()['id'],
            'status' => $a
        ];

        $rent = new RentModel;
        $orders = $rent->getRentalsByRentalService($data);

        $this->view('rental/components/orderlist', ['orders' => $orders]);
    


    }
}


?>