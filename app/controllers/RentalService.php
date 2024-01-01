<?php

class RentalService {
    use Controller;


    public function update(string $a = '', string $b = '', string $c = ''):void {

        $rentalservice = new rentalserviceModel();
        $rentalservice->updateRentalservice($_POST);

        redirect('dashboard');
        // $this->view('customer/profile');
    }
}