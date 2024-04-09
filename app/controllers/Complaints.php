<?php

class Complaints{
    use Controller;

    public function index(){
        
        $this->view('rental/complaints');
    }

    public function returnComplaintsbyRentalService(string $a = '', string $b = '', string $c = ''):void {
    
        $rentreturncomplaint = new RentReturnComplaintModel;
        $data['complaints'] = $rentreturncomplaint->getComplaintsByRentalId(UserMiddleware::getUser()['id']);

        $this->view('rental/components/complainlist', $data);

        

        


    }

    public function viewComplaint(string $a = '', string $b = '', string $c = ''):void {
        $rentreturncomplaint = new RentReturnComplaintModel;
        $complaint = $rentreturncomplaint->getComplaint($a);
        // show($complaint);
        $rent = new RentModel;
        $order = $rent->getRental($complaint->rent_id);
        $items = $rent->getItemListbyRentId($complaint->rent_id);

        // show(['complaint' => $complaint, 'order' => $order, 'items' => $items]);

        $this->view('rental/components/complaint', ['complaint' => $complaint, 'order' => $order, 'items' => $items]);
    }
}