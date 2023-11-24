<?php

class Signup {
    use Controller;

    public function index(): void {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new UserModel;

            $user->registerUser($_POST);

            $data['errors'] = $user->errors;
        }

        $this->view('signup', $data);
    }

    public function rentalService(): void {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rental = new RentalServiceModel;

            $rental->registerRentalService($_POST,$_FILES);

            $data['errors'] = $rental->errors;
        }

        $this->view('login', $data);

    }

    public function customer(): void {
        $data = [];
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customerModel = new CustomerModel;

            $request = new JSONRequest;
            $response = new JSONResponse;
            
            $customerModel->registerCustomer($request, $response);
    
            // $customerModel->registerCustomer($_POST);
    
            $data['errors'] = $customerModel->errors;
        }
    
        $this->view('login', $data);
    }

    public function guide(): void {
        $data = [];
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $guideModel = new GuideModel;
    
            $guideModel->registerGuide($_POST);
    
            $data['errors'] = $guideModel->errors;
        }
    
        $this->view('signup', $data);
    }
    


}

?>
