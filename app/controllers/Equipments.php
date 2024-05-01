<?php


class Equipments{
    use Controller;

    public function index(){
        
        AuthorizationMiddleware::authorize(['rentalservice']);
        $this->view('rental/equipments');
    }
}



?>