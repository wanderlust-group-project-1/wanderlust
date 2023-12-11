<?php

class Rental {
    use Controller;

    public function index(): void {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new UserModel;

            $user->registerUser($_POST);
            echo "success";


            $data['errors'] = $user->errors;
        }

    }

    public function updateStatus():void {
        $request = new JSONRequest;
        $response = new JSONResponse;

        $rental = new RentalServiceModel;
        // echo "success";
        // show($request->getAll());

        $rental->updateStatus($request, $response);
    }

    
    


}

?>
