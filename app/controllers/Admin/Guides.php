<?php

class Guides {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        
        $guide = new GuideModel();
        $data['guides'] = $guide->findAll();

        $this->view('admin/guides', $data);
    }

    public function item(string $a = '', string $b = '', string $c = ''):void {
        $this->view('admin/guides');
    }}


?>