<?php

class Guide {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('guide');
    }

    public function find(string $a = '', string $b = '', string $c = ''):void {
        $this->view('guide/find');
    }


}