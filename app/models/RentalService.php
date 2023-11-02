<?php

class RentalServiceModel {
    use Model;

    protected string $table = 'rental_services';
    protected array $allowedColumns = [
        'name',
        'address',
        'regNo',
        'mobile',
        'user_id',
        // 'verification_document',
        // 'email',
        // 'password',
    ];


    public function registerRentalService(array $data,array $files){

        show($data);
        show($files);
        die();

        if ($this->validateRentalService($data)) {
            $user = new UserModel;

            $data['user_id'] = $user->registerUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'rentalservice',
            ]);


            // uuid
            upload($files['verification_document'],'rental_services');
           


            if($data['user_id']){
                $data = array_filter($data, function ($key) {
                    return in_array($key, $this->allowedColumns);
                }, ARRAY_FILTER_USE_KEY);

                return $this->insert($data);
            }
        }





    //     if ($this->validateRentalService($data)) {


    //         $data['user_id'] = $this->registerUser([
    //             'email' => $data['email'],
    //             'password' => $data['password'],
    //         ]);

    //         if ($data['user_id']) {

    //             // only allowed columns
    //             $data = array_filter($data, function ($key) {
    //                 return in_array($key, $this->allowedColumns);
    //             }, ARRAY_FILTER_USE_KEY);


         
    //             return $this->insert($data);
    //         }
            

    //     return false;
    // }

    }


    public function validateRentalService(array $data){
        $this->errors = [];

        if(empty($data['name'])){
            $this->errors['name'] = "Name is required";
        }

        if(empty($data['address'])){
            $this->errors['address'] = "Address is required";
        }

        if(empty($data['regNo'])){
            $this->errors['regNo'] = "Registration Number is required";
        }

        if(empty($data['mobile'])){
            $this->errors['mobile'] = "Mobile Number is required";
        }
        if(empty($data['verification_document'])){
            $this->errors['verification_document'] = "Verification Document is required";

        }

        if(empty($data['email'])){
            $this->errors['email'] = "Email is required";
        }else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $this->errors['email'] = "Email is not valid";
        }


        if(empty($data['password'])){
            $this->errors['password'] = "Password is required";
        }else if(strlen($data['password']) < 6){
            $this->errors['password'] = "Password must be at least 6 characters";
        }
         
        return empty($this->errors);
    }

    public function updateRentalservice(array $data)
    {

        // $user = new UserModel;

        $data['id'] = $_SESSION['USER']->id;

        // alowed column
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
    
        return $this->update($_SESSION['USER']->id, $data, 'id');
    }
    
}