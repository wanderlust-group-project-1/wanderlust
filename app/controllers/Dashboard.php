<?php

class Dashboard
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $user = UserMiddleware::getUser();
        // echo $user->role;

        if ($user['role'] == 'guide') {
            //echo "Welcome";
            $this->view('guide/GuideDashboard');
        } else if ($user['role'] == 'rentalservice') {


            $rental = new RentalServiceModel;
            $data = [
                'stat' => $rental->rentalStats($user['id'])[0]
            ];

            // show($user->id);
            // show($data);





            $this->view('rental/RentalDashboard',$data);
        }

        // else {
        //     $this->view('profile');
        // }

        // $this->view('profile');
    }

    public function equipments(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('rental/equipments');
    }

    public function rents(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('rental/rents');
    }
}
