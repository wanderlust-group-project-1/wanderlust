<?php

class RentalServices {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {

        $rentalServices = new RentalServiceModel();
        $data['rentalServices'] = $rentalServices->findAll();
        // show($data);

        $this->view('admin/rentalServices', $data);
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/item');
    }}


?>