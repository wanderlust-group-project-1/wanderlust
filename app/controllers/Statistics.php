<?php

class Statistics
{
    use Controller;


    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        AuthorizationMiddleware::authorize(['rentalservice']);

        $rental = new RentalServiceModel;
        $data = [
            'stat' => $rental->rentalStats(UserMiddleware::getUser()['id'])[0]
        ];


        $this->view('rental/statistics', $data);
    }
}
