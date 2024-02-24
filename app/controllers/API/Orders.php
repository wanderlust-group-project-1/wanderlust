<?php 

class Orders {
    use Controller;


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


}