<?php

class CustomerModel {
    use Model;

    protected string $table = 'customers';
    protected array $allowedColumns = [
        'name',
        'address',
        'number',
        'nic',
    ];

    public function registerCustomer(array $data){
        if ($this->validateCustomerSignup($data)) {
            $user = new UserModel;

            $data['user_id'] = $user->registerUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'customer',
            ]);


            if($data['user_id']){
                $data = array_filter($data, function ($key) {
                    return in_array($key, $this->allowedColumns);
                }, ARRAY_FILTER_USE_KEY);


                return $this->insert($data);

            }


        }
        return false;
    }

    public function validateCustomerSignup(array $data){
        $this->errors = [];

        if(empty($data['name'])){
            $this->errors['name'] = "Name is required";
        }

        if(empty($data['address'])){
            $this->errors['address'] = "Address is required";
        }

        if(empty($data['email'])){
            $this->errors['email'] = "Email is required";
        } else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $this->errors['email'] = "Email is not valid";
        }

        if(empty($data['number'])){
            $this->errors['number'] = "Number is required";
        }

        if(empty($data['nic'])){
            $this->errors['nic'] = "NIC Number is required";
        }

        if(empty($data['password'])){
            $this->errors['password'] = "Password is required";
        } else if(strlen($data['password']) < 6){
            $this->errors['password'] = "Password must be at least 6 characters";
        }

        return empty($this->errors);
    }
}
