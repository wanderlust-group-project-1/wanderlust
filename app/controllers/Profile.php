<?php

class Profile {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $this->view('profile');
    }
}