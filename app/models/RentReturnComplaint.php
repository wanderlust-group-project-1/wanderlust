<?php


class RentReturnComplaintModel
{
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

    public function createReturnComplaint($data)
    {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        $this->insert($data);
    }

    public function getComplaintsByRentalId($rental_id, $status='pending') {
        
        $q = new QueryBuilder;
        // join rent table to get rental
        $q->setTable('rent_return_complaints');
        $q->select('rent_return_complaints.*,rent.id as rent_id')
            ->setTable('rent_return_complaints')
            ->join('rent', 'rent_return_complaints.rent_id', 'rent.id')
            ->where('rent.rentalservice_id', $rental_id)
            ->where('rent_return_complaints.status', $status);

        // echo $q->getQuery();
        return $this->query($q->getQuery(), $q->getData());
    }

    public function getComplaint($id)
    {
        $q = new QueryBuilder;
        $q->setTable('rent_return_complaints');
        $q->select('rent_return_complaints.*,rent.id as rent_id')
            ->join('rent', 'rent_return_complaints.rent_id', 'rent.id')
            ->where('rent_return_complaints.id', $id);

        return $this->query($q->getQuery(), $q->getData())[0];
    }

    public function cancelComplaint($id)
    {
        $q = new QueryBuilder;
        $q->setTable('rent_return_complaints');

        $q->update(['status' => 'cancelled'])
            ->where('id', $id);

        return $this->query($q->getQuery(),$q->getData());

        
    }

    public function getComplaintsByCustomerId($id) {
        $q = new QueryBuilder;
        $q->setTable('rent_return_complaints');
        $q->select('rent_return_complaints.*,rent.id as rent_id');
        $q->join('rent', 'rent_return_complaints.rent_id', 'rent.id');
        $q->where('rent.customer_id', $id);

        return $this->query($q->getQuery(),$q->getData());

    }



  


    public function getAdminRentalComplaints($status = "pending"){ 

            $q = new QueryBuilder;
            // join rent table to get rental
            $q->setTable('rent_return_complaints');
            $q->select('rent_return_complaints.*,rent.id as rent_id')
                ->setTable('rent_return_complaints')
                ->join('rent', 'rent_return_complaints.rent_id', 'rent.id')
                ->where('rent_return_complaints.status', $status);

            // echo $q->getQuery();
            return $this->query($q->getQuery(), $q->getData());
        }
    
}
