<?php

class Guides {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/guides');
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/guides');
    }}


?>