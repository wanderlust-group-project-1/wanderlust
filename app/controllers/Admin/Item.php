<?php

class Items
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $item = new ItemModel();
        $data['items'] = $item->findAll();
        //show($data);

        $this->view('admin/rentalServices', $data);
    }

    public function item(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('admin/rentalServices/item');
    }
}
