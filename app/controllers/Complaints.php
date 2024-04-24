<?php

class Complaints{
    use Controller;

    public function index(){
        
        // show (UserMiddleware::getUser());
        if (UserMiddleware::getUser()['role']=='rentalservice') {
            $this->view('rental/complaints');
        }elseif (UserMiddleware::getUser()['role']=='customer') {
            $this->view('customer/complaints');
        }
        
    }

    //Rental service
    public function returnComplaintsbyRentalService(string $a = '', string $b = '', string $c = ''):void {
    
        $rentreturncomplaint = new RentReturnComplaintModel;
        $data['complaints'] = $rentreturncomplaint->getComplaintsByRentalId(UserMiddleware::getUser()['id'], $a);

        $this->view('rental/components/complainlist', $data);

      
    }

    public function viewRentComplaint(string $a = '', string $b = '', string $c = ''):void {
        $rentreturncomplaint = new RentReturnComplaintModel;
        $complaint = $rentreturncomplaint->getComplaint($a);
        // show($complaint);
        $rent = new RentModel;
        $order = $rent->getRental($complaint->rent_id);
        $items = $rent->getItemListbyRentId($complaint->rent_id);

        // show($items);
        // show($complaint);

        // add complaint details to items
        foreach ($items as $item) {
            $item->complaint = null;
            $item->charge = 0;
            // show(json_decode($complaint->complains));
            foreach (json_decode($complaint->complains) as $complain) {
                if ($item->equipment_id == $complain->equipment_id) {
                    $item->complaint = $complain;
                    $item->charge = $complain->charge;
                }
            }
        }



        // show(['complaint' => $complaint, 'order' => $order, 'items' => $items]);

        $this->view('rental/components/complaint', ['complaint' => $complaint, 'order' => $order, 'items' => $items]);
    }

    


    //Customer
    public function returnComplaintsbyCustomer(string $a = '', string $b = '', string $c = ''):void {
    
        $rentreturncomplaint = new RentReturnComplaintModel;
        $data['complaints'] = $rentreturncomplaint->getComplaintsByCustomerId(UserMiddleware::getUser()['id']);

        $this->view('customer/components/complaintlist', $data);

      

        


    }
    
    public function returnComplaintbyCustomer(string $a = '', string $b = '', string $c = ''):void {
        $rentreturncomplaint = new RentReturnComplaintModel;
        // show ($a);
        $complaint = $rentreturncomplaint->getComplaint($a);
        // show($complaint);
        $rent = new RentModel;
        $order = $rent->getRental($complaint->rent_id);
        $items = $rent->getItemListbyRentId($complaint->rent_id);


        // show($items);
        // show($complaint);


        // add complaint details to items
        foreach ($items as $item) {
            $item->complaint = null;
            $item->charge = 0;
            // show(json_decode($complaint->complains));
            foreach (json_decode($complaint->complains) as $complain) {
                if ($item->equipment_id == $complain->equipment_id) {
                    $item->complaint = $complain;
                    $item->charge = $complain->charge;
                }
            }
        }



        // show(['complaint' => $complaint, 'order' => $order, 'items' => $items]);

        $this->view('customer/components/complaint', ['complaint' => $complaint, 'order' => $order, 'items' => $items]);
    }

}