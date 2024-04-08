<?php


class RentReturnComplaintModel {
    use Model;



    // CREATE TABLE rent_return_complaints (
    //     id INT AUTO_INCREMENT PRIMARY KEY,
    //     rent_id INT NOT NULL,
    //     complain JSON NOT NULL,
    //     charge DECIMAL(10, 2) NOT NULL
    // );


    // var data = {
    //     order_id: orderId,
    //     complaints: [],
    //     complaint_descriptions: [],
    //     charges: []
    // };
    


    protected string $table = 'rent_return_complaints';

    protected array $allowedColumns = [
        'rent_id',
        'complains',
        'charge'
    ];

    public function createReturnComplaint($data) {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        $this->insert($data);


    
       






    }

    public function getComplaintsByRentalId($rental_id) {
        
        $q = new QueryBuilder;
        // join rent table to get rental
        $q->setTable('rent_return_complaints');
        $q->select('rent_return_complaints.*,rent.id as rent_id')
            ->setTable('rent_return_complaints')
            ->join('rent', 'rent_return_complaints.rent_id', 'rent.id')
            ->where('rent.rentalservice_id', $rental_id);

        // echo $q->getQuery();
        return $this->query($q->getQuery(),$q->getData());
    }


}