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
    
    }

?>