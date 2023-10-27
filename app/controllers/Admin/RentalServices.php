<?php

class RentalServices {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/rentalServices');
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/item');
    }}


?>