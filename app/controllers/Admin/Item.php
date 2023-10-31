<?php

class Item {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/item');
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/item');
    }}


?>