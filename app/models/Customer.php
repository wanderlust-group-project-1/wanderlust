<?php

class CustomerModel
{
    use Model;

    protected string $table = 'customers';
    protected array $allowedColumns = [
        'name',
        'address',
        'number',
        'nic',
        'user_id'
    ];

    // public function registerCustomer(array $data){
    //     if ($this->validateCustomerSignup($data)) {
    //         $user = new UserModel;

    //         $data['user_id'] = $user->registerUser([
    //             'email' => $data['email'],
    //             'password' => $data['password'],
    //             'role' => 'customer',
    //         ]);


    //         if($data['user_id']){
    //             $data = array_filter($data, function ($key) {
    //                 return in_array($key, $this->allowedColumns);
    //             }, ARRAY_FILTER_USE_KEY);


    //             return $this->insert($data);

    //         }


    //     }
    //     return false;
    // }


    public function registerCustomer(JSONRequest $request, JSONResponse $response)
    {
        $data = $request->getAll();

        if ($this->validateCustomerSignup($data)) {
            $user = new UserModel;

            $data['user_id'] = $user->registerUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'customer',
            ]);

            if ($data['user_id']) {
                $data = array_filter($data, function ($key) {
                    return in_array($key, $this->allowedColumns);
                }, ARRAY_FILTER_USE_KEY);

                $this->insert($data);

                $response->success(true)
                    ->data(['user_id' => $data['user_id']])
                    ->message('Customer registered successfully')
                    ->statusCode(201)
                    ->send();
            } else {
                $response->success(false)
                    ->message('User registration failed')
                    ->statusCode(500)
                    ->send();
            }
        } else {
            $response->success(false)
                ->data(['errors' => $this->errors])
                ->message( isset($this->errors['msg']) ? $this->errors['msg'] : 'Validation failed')
                ->statusCode(422)
                ->send();
        }
    }





    public function validateCustomerSignup(array $data)
    {
        $this->errors = [];

        if (empty($data['name'])) {
            $this->errors['name'] = "Name is required";
        }

        if (empty($data['address'])) {
            $this->errors['address'] = "Address is required";
        }

        if (empty($data['email'])) {
            $this->errors['email'] = "Email is required";
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Email is not valid";
        }

        if (empty($data['number'])) {
            $this->errors['number'] = "Number is required";
        }

        if (empty($data['nic'])) {
            $this->errors['nic'] = "NIC Number is required";
        }

        if (empty($data['password'])) {
            $this->errors['password'] = "Password is required";
        } else if (strlen($data['password']) < 6) {
            $this->errors['password'] = "Password must be at least 6 characters";
        }

        // check if email already exists
        $user = new UserModel;
        if ($user->first(['email' => $data['email']])) {
            $this->errors['email'] = "Email already exists";
            $this->errors['msg'] = "Email already exists";
        }
        
        $customer = new CustomerModel;
        if($customer->first(['nic' => $data['nic']])){
            $this->errors['nic'] = "NIC Number already exists";
            $this->errors['msg'] = "NIC Number already exists";
        }

        if($customer->first(['number' => $data['number']])){
            $this->errors['number'] = "Mobile Number already exists";
            $this->errors['msg'] = "Mobile Number already exists";
        }


        return empty($this->errors);
    }

    public function updateCustomer(array $data)
    {

        // $user = new UserModel;

        $data['id'] = $_SESSION['USER']->id;

        // alowed column
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->update($_SESSION['USER']->id, $data, 'id');
    }


    public function uploadImage(array $data, int $id): mixed
    {
        $data['image'] =  upload($data['image'], 'images/customers');
        $this->update($id, ['image' => $data['image']]);
        return $data;
    }




    public function getCustomer(int $id): mixed
    {
        $q = new QueryBuilder;
        $q->setTable('customers');
        // with user table join
        $q->select('customers.*,users.email,users.role')
            // ->from('rental_services')
            ->join('users', 'customers.user_id', 'users.id')
            ->where('customers.id', $id);
        // return $this->query();
        // show($id);
        // show($q->getQuery());
        return $this->query($q->getQuery(), $q->getData());
    }

    // public function validateCustomerUpdate($data){
    //     $this->errors = [];

    //     if(empty($data['name'])){
    //         $this->errors['name'] = "Name is required";
    //     }

    //     if(empty($data['address'])){
    //         $this->errors['address'] = "Address is required";
    //     }

    //     if(empty($data['number'])){
    //         $this->errors['number'] = "Number is required";
    //     }

    //     if(empty($data['nic'])){
    //         $this->errors['nic'] = "NIC Number is required";
    //     }

    //     return empty($this->errors);
    // }


}
