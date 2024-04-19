<?php

class MyOrders {
    use Controller;

    


    public function markAsRentedByCustomer(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'customer_req' => 'rented',
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['rent_id' => $a])->send();


        
    }

    public function cancelOrder(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'customer_req' => NULL,
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['rent_id' => $a])->send();

    }
}