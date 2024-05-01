<?php

class Orders {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {


        AuthorizationMiddleware::authorize(['rentalservice']);
        $this->view('rental/orders');
    }

    public function list(string $a = '', string $b = '', string $c = ''):void {


        AuthorizationMiddleware::authorize(['rentalservice']);
        $data = [
            'rentalservice_id' => UserMiddleware::getUser()['id'],
            'status' => $a
        ];

        $rent = new RentModel;
        $orders = $rent->getRentalsByRentalService($data);

        $this->view('rental/components/orderlist', ['orders' => $orders]);
    


    }

    public function viewOrder(string $a = '', string $b = '', string $c = ''):void {

        AuthorizationMiddleware::authorize(['rentalservice']);
        $rent = new RentModel;
        $order = $rent->getRental($a);
        $items = $rent->getItemListbyRentId($a);





        // show($order);

        $this->view('rental/components/order', ['order' => $order, 'items' => $items]);
    }



    // Complains by rental service

    public function reportReturnComplaintByRentalservice(string $a = '', string $b = '', string $c = ''):void {


        AuthorizationMiddleware::authorize(['rentalservice']);
        $rent = new RentModel;
        $order = $rent->getRental($a);
        $items = $rent->getItemListbyRentId($a);

        $this->view('rental/components/reportReturnIssue', ['order' => $order, 'items' => $items]);
    }



}


?>