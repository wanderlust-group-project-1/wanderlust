<?php 

class Complaints{
    use Controller;

    public function index(){
        
        $this->view('rental/complaints');
    }

    public function cancelComplaint(string $a = '', string $b = '', string $c = ''):void {

    $complaint = new RentReturnComplaintModel;
    $complaint->cancelComplaint($a);

    $response = new JSONResponse;
    $response->statusCode(200)->data(['complaint_id' => $a])->send();

        
        
        

    }
}