<?php

class MyOrders {
    use Controller;

    
    public function index(string $a = '', string $b = '', string $c = ''):void {

        

      
        $this->view('customer/orders');
    }

    public function list(string $a = '', string $b = '', string $c = ''):void {
        $data = [
            'customer_id' => UserMiddleware::getUser()['id'],
            'status' => $a
        ];

        $rent = new RentModel;
        $orders = $rent->getRentalsByCustomer($data);

        $this->view('customer/components/orderlist', ['orders' => $orders]);
    }

    public function viewOrder(string $a = '', string $b = '', string $c = ''):void {
 
        $rent = new RentModel;
        $order = $rent->getRental($a);
        $items = $rent->getItemListbyRentId($a);


        $this->view('customer/components/order', ['order' => $order, 'items' => $items]);
    }

}


    


?>