<?php

class Complains
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('admin/complains');
    }

    public function listComplains(string $a = '', string $b = '', string $c = ''): void
    {
        //who complaints
        if($a == 'customer'){
            $complaint = new RentComplaintModel;
            $data['complaints'] = $complaint->getComplaints (['status' => $b]);
            $data['view'] = '-customer';

            $this->view('admin/components/complainlist', $data);

        }else if($a == 'rentalservice'){
            $complain = new RentReturnComplaintModel;
            $data['complaints'] = $complain->getAdminRentalComplaints($b);
            $data['view'] = '';
    
            $this->view('admin/components/complainlist', $data);
        }

        
    }
    

    public function viewRentalComplaint(string $a = '', string $b = '', string $c = ''): void
    {
        $rentreturncomplaint = new RentReturnComplaintModel;
        $complaint = $rentreturncomplaint->getComplaint($a);
        // show($complaint);
        $rent = new RentModel;
        $order = $rent->getRental($complaint->rent_id);
        $items = $rent->getItemListbyRentId($complaint->rent_id);

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

        $this->view('admin/components/complaint', ['complaint' => $complaint, 'order' => $order, 'items' => $items]);
    }


}
