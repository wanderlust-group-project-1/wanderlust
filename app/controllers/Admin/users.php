<?php

class users
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $guide = new GuideModel();
        $data['guides'] = $guide->findAll();

        $this->view('admin/users', $data);
    }

    public function item(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('admin/users');
    }

    // public function viewUser(string $a = '', string $b = '', string $c = ''): void
    // {
    //     $guide = new GuideModel();



    //     $data['guide'] = $guide->getGuide($a)[0];
    //     // // show(  $data['rental']);
    //     $this->view('admin/guides/user', $data);
    // }
}
