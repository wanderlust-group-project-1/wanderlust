<?php

class Charts {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/chart');
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/chart');
    }}


?>