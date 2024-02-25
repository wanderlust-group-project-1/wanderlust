<?php 

class Orders {
    use Controller;


    // Rental Service

    public function markAsRentedByRentalservice(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'rentalservice_req' => 'rented',
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

        



    }

    public function cancelRentedByRentalservice(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'rentalservice_req' => NULL,
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

    }


    public function markAsReturnedByRentalservice(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'rentalservice_req' => 'completed',
        ];

        // show($a);

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

    }


    //   pending 

    public function acceptRequestByRentalservice(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'rentalservice_req' => 'accepted',
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

    }

    public function cancelRequestByRentalservice(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'rentalservice_req' => 'cancelled',
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

    }



    // Customer

    public function markAsRentedByCustomer(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'customer_req' => 'rented',
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

    }

    public function markAsReturnedByCustomer(string $a = '', string $b = '', string $c = ''):void {

        $response = new JSONResponse();

        $data = [
            'customer_req' => 'completed',
        ];

        $rentReq = new RentRequestModel;
        $rentReq->updateRequest($a, $data);

        $response->statusCode(200)->data(['order_id' => $a])->send();

    }


}