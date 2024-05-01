<?php

class Dashboard
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {


        AuthorizationMiddleware::authorize(['guide', 'rentalservice']);

        $user = UserMiddleware::getUser();
        // echo $user->role;

        if ($user['role'] == 'guide') {
            //echo "Welcome";
            $this->view('guide/GuideDashboard');
        } else if ($user['role'] == 'rentalservice') {


            $rental = new RentalServiceModel;
            $rent = new RentModel;

            $data = [
                'stat' => $rental->rentalStats($user['id'])[0],
                'rent' => $rent->getUpcomingRentByRentalService(['rentalservice_id' => $user['id']])
            ];

            // show($user->id);
 




            $this->view('rental/RentalDashboard',$data);
        }

        // else {
        //     $this->view('profile');
        // }

        // $this->view('profile');
    }

    public function equipments(string $a = '', string $b = '', string $c = ''): void
    {

       
        AuthorizationMiddleware::authorize(['rentalservice']);
        $this->view('rental/equipments');
    }

    public function rents(string $a = '', string $b = '', string $c = ''): void
    {
        AuthorizationMiddleware::authorize(['rentalservice']);
        $this->view('rental/rents');
    }
}
