<?php

class Dashboard
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $user = $_SESSION['USER'];
        // echo $user->role;

        if ($user->role == 'guide') {
            //echo "Welcome";
            $this->view('guide/GuideDashboard');
        } else if ($user->role == 'rentalservice') {
            $this->view('rental/RentalDashboard');
        }

        // else {
        //     $this->view('profile');
        // }

        // $this->view('profile');
    }
}
