<?php

class Customer {
    use Controller;


    public function update(string $a = '', string $b = '', string $c = ''):void {

        $customer = new CustomerModel();
        $customer->updateCustomer($_POST);



        $this->view('customer/profile');
    }
}