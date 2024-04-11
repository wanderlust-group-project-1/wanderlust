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

        // show($items);
        // show($complaint);


//         Array
// (
//     [0] => stdClass Object
//         (
//             [equipment_id] => 25
//             [equipment_name] => Tent - 2 Persons
//             [item_number] => I000254121
//             [equipment_cost] => 3040.00
//         )

//     [1] => stdClass Object
//         (
//             [equipment_id] => 33
//             [equipment_name] => Torch 99
//             [item_number] => I000334741
//             [equipment_cost] => 4000.00
//         )

// )
// stdClass Object
// (
//     [id] => 3
//     [rent_id] => 68
//     [complains] => [{"charge": "2830", "equipment_id": "25", "complaint_description": "Beer - Hyatt"}, {"charge": "2524", "equipment_id": "33", "complaint_description": "Schoen and Sons"}]
//     [charge] => 5354.00
//     [description] => 
//     [status] => pending
//     [created_at] => 2024-04-11 08:17:39
// )

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
}