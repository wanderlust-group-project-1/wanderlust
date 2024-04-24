<?php
    class RentComplaintModel {
        use Model;

        protected string $table = 'rent_complaint';

        protected array $allowedColumns = [
           'rent_id',
            'title',
            'description',
            'status'
        ];

        public function createComplaint($data) {
            $data = array_filter($data, function ($key) {
                return in_array($key, $this->allowedColumns);
            }, ARRAY_FILTER_USE_KEY);

            $this->insert($data);
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
    
    }

?>