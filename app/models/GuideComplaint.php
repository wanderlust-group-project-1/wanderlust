<?php

class GuideComplaintModal{
    use Model;

    protected string $table = 'guide_complaints';

    protected array $allowedColumns = [
        'guide_id',
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


    public function getComplaints(array $data)
    {
        return $this->where($data);
    }

    public function getComplaintsByCustomer(array $data)
    {
        $q = new QueryBuilder;
        $q->setTable('guide_complaints');
        $q->select('guide.guide_id as guide_id, guide.id as guide_id, complaint_no as complaint_no, guide_complaint.status as status, guide_complaint.description as description, guide_complaint.created_at as created_at')
            ->join('guide', 'guide_complaint.guide_id', 'guide.id')
            ->where('guide.guide_id', $data['guide_id']);

        return $this->query($q->getQuery(), $q->getData());
    }

    public function getGuideComplaint($id)
    {
        $q = new QueryBuilder;
        $q->setTable('guide_complaint');
        $q->select('guide_complaint.complaint_no as complaint_no, guide.id as guide_id,guide.created_at as guide_date, guide.guide_id as guide_id, guide.paid_amount as paid_amount, guide.created_at as paid_date, guide.start_date as start_date, guide.end_date as end_date, 
            guide.status as guide_status, guide.total as total_amount,guide_services.id as guide_id, guide_services.name as guide_name, guide_services.mobile as guide_mobile,
            guide_complaint.title as title, guide_complaint.description as description, guide_complaint.created_at as created_at, guide_complaint.status as status')
            ->join('guide', 'guide_complaint.guide_id', 'guide.id')
            ->join('guide_services', 'guide.guide_id', 'guide_services.id')
            ->where('guide_complaint.complaint_no', $id);

        return $this->query($q->getQuery(), $q->getData());
    }
}