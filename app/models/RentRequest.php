<?php


class RentRequestModel {
    use Model;



    protected string $table = 'rent_request';
    protected array $allowedColumns = [
        'rent_id',
        'rentalservice_req',
        'customer_req',
    ];


    public function updateRequest($id,$data){
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

    // show($data);
    // show($id);
    // die();

        
        return $this->update($id,$data,'rent_id');
        




    }


}