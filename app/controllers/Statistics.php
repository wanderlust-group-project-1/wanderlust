<?php

class Statistics
{
    use Controller;


    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        AuthorizationMiddleware::authorize(['rentalservice']);
        $user = UserMiddleware::getUser();


        $rental = new RentalServiceModel;
        $rent = new RentModel;

        $data = [
            'stat' => $rental->rentalStats(UserMiddleware::getUser()['id'])[0],
            'rent' => $rent->getUpcomingRentByRentalService(['rentalservice_id' => $user['id']])


        ];


        $this->view('rental/statistics', $data);
    }
}
