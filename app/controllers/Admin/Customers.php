<?php

class Customers
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $customer = new CustomerModel();
        $data['customers'] = $customer->findAll();
        // show($data);

        $this->view('admin/customer', $data);
    }

    // public function index(string $a = '', string $b = '', string $c = ''): void
    // {
    //     $this->view('admin/customer');
    // }

    public function user(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('admin/customer');
    }
    public function viewUser(string $a = '', string $b = '', string $c = ''): void
    {
        $customer = new CustomerModel();
        $data['customer'] = $customer->getCustomer($a)[0];
        // show(  $data['rental']);
        $this->view('admin/customer/user', $data);
    }
}
