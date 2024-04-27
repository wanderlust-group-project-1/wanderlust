<?php
class RentComplaintModel
{
    use Model;

    protected string $table = 'rent_return_complaints';

    protected array $allowedColumns = [
        'rent_id',
        'title',
        'description',
        'status'
    ];

    public function createComplaint($data)
    {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        $this->insert($data);
    }

    public function resolveComplaint($id)
    {

        // $data = array_filter($data, function ($key) {
        //     return in_array($key, $this->allowedColumns);
        // }, ARRAY_FILTER_USE_KEY);
        //$table
        $this->update($id, ['status' => 'resolved']);
    }
        public function getComplaints(array $data) {
            return $this->where($data);
        }

        public function getComplaintsByCustomer(array $data) {
            $q = new QueryBuilder;
            $q->setTable('rent_complaint');
            $q->select('rent.customer_id as customer_id, rent.id as rent_id, complaint_no as complaint_no, rent_complaint.status as status, rent_complaint.description as description, rent_complaint.created_at as created_at')
            ->join('rent', 'rent_complaint.rent_id','rent.id')
            ->where('rent.customer_id', $data['customer_id']);

            return $this->query($q->getQuery(), $q->getData());
        }

        public function getRentComplaint($id) {
            $q = new QueryBuilder;
            $q->setTable('rent_complaint');
            $q->select('rent_complaint.complaint_no as complaint_no, rent.id as rent_id,rent.created_at as rent_date, rent.customer_id as customer_id, rent.paid_amount as paid_amount, rent.created_at as paid_date, rent.start_date as start_date, rent.end_date as end_date, 
            rent.status as rent_status, rent.total as total_amount,rental_services.id as rental_id, rental_services.name as rental_name, rental_services.mobile as rental_mobile,
            rent_complaint.title as title, rent_complaint.description as description, rent_complaint.created_at as created_at, rent_complaint.status as status')
            ->join('rent','rent_complaint.rent_id', 'rent.id')
            ->join('rental_services', 'rent.rentalservice_id','rental_services.id')
            ->where('rent_complaint.complaint_no', $id);

            return $this->query($q->getQuery(), $q->getData());
        }
    


    public function getComplaints(array $data)
    {
        return $this->where($data);
    }
}
