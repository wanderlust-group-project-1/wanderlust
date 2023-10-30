<?php

class Customer {
    use Controller;


    public function update(string $a = '', string $b = '', string $c = ''):void {




        $this->view('customer/profile');
    }
}