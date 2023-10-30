<?php

class Customers {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/customer');
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/customer');
    }}


?>