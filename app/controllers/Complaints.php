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
}