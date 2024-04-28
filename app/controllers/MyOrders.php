<?php

class MyOrders {
    use Controller;

    
    public function index(string $a = '', string $b = '', string $c = ''):void {

        

        AuthorizationMiddleware::authorize(['customer']);
      
        $this->view('customer/orders');
    }

    public function list(string $a = '', string $b = '', string $c = ''):void {
        AuthorizationMiddleware::authorize(['customer']);

        $data = [
            'customer_id' => UserMiddleware::getUser()['id'],
            'status' => $a
        ];

        $rent = new RentModel;
        $orders = $rent->getRentalsByCustomer($data);

        $this->view('customer/components/orderlist', ['orders' => $orders]);
    }

    public function viewOrder(string $a = '', string $b = '', string $c = ''):void {
 
        AuthorizationMiddleware::authorize(['customer']);

        $rent = new RentModel;
        $order = $rent->getRental($a);
        $items = $rent->getItemListbyRentId($a);

        $complaint = new RentComplaintModel;
        $complaints = $complaint->getComplaints(['rent_id' => $a]);

        $this->view('customer/components/order', ['order' => $order, 'items' => $items, 'complaints' => $complaints]);
    }

    public function fullPay(string $a = '', string $b = '', string $c = ''):void {
        AuthorizationMiddleware::authorize(['customer']);

        $rent = new RentModel;
        $data['rent'] = $rent->first(['id' => $a]);

        $this->view('customer/components/fullpay', $data);
    }






}


    


?>