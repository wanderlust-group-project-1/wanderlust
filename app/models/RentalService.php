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
        'verification_document',
        'location_id',

        // 'email',
        // 'password',
    ];


    public function registerRentalService(JSONRequest $request, JSONResponse $response) {
        $data = $request->getAll();
        $files = $_FILES; // Assuming the files are sent as part of the request


        // show($files);
        // show($request->getAll());
        if ($this->validateRentalService($data,$files)) {
            $user = new UserModel;

            $data['user_id'] = $user->registerUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'rentalservice',
            ]);


            $location = new LocationModel;
            // show($data);
            $data["location_id"] =  $location->createLocation(
                $data['latitude'],
                $data['longitude']
            );

            if ($data['user_id']) {
                $data['verification_document'] = upload($files['verification_document'], 'rental_services');

                $data = array_filter($data, function ($key) {
                    return in_array($key, $this->allowedColumns);
                }, ARRAY_FILTER_USE_KEY);

                $this->insert($data);

                $response->success(true)
                    ->data(['user_id' => $data['user_id']])
                    ->message('Rental service registered successfully')
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
                ->message('Validation failed')
                ->statusCode(422)
                ->send();
        }
    }


    public function validateRentalService(array $data,array $files){
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
        if(empty($files['verification_document'])){
            $this->errors['verification_document'] = "Verification Document is required";

        }
        //check file available or not 


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

    public function getRentalService(int $id): mixed {
        $q = new QueryBuilder;
        $q->setTable('rental_services');
        // with user table join
        $q->select('rental_services.*,users.email,users.role')
            // ->from('rental_services')
            ->join('users', 'rental_services.user_id', 'users.id')
            ->where('rental_services.id', $id);
        // return $this->query();
        // show($id);
        // show($q->getQuery());
        return $this->query($q->getQuery(),$q->getData());
    }

    // Get Renatal Service Stat
    public function getRentalServiceStat(int $id): mixed {
        // orders count
        // total equipments

        $q = new QueryBuilder;
        $q->setTable('rental_services');
        //  group by rental service id and count orders
        $q->select('count(distinct(rent.id)) as orders_count , count(distinct(equipment.id)) as equipments_count')
            ->join('equipment', 'rental_services.id', 'equipment.rentalservice_id')
            ->join('rent', 'rental_services.id', 'rent.rentalservice_id')
            ->where('rental_services.id', $id)
            ->groupBy('rental_services.id');

        return $this->query($q->getQuery(),$q->getData());
            
    }

    // Get rental service for customer

    // public function getRentalServiceforCustomer(int $id): mixed {


    // }


    public function updateStatus(JSONRequest $request, JSONResponse $response){
        $data = $request->getAll();



        $newStatus = $data['newStatus'];

        // show($newStatus);
        $q = new QueryBuilder;
        $q->setTable('rental_services');
        $q->update(['status' => $newStatus])
            ->where('id', $data['userId']);
        // echo $q->getQuery();

        $this->query($q->getQuery(),$q->getData());
        
        // show($q->getQuery());
        // show($q->getData());
        $response->success(true)
            ->data(['newStatus' => $newStatus])
            ->message('Status updated successfully')
            ->statusCode(200)
            ->send();

    }

    public function rentalStats(int $id): mixed {

        $q = "CALL getRentalStats(:id)";


        return $this->query($q,['id' => $id]);

  
            
    }

    // public function updateRentalService(JSONRequest $request, JSONResponse $response) {
    //     $data = $request->getAll();

    //     if ($this->validateRentalService($data)) {
    //         $data['id'] = $_SESSION['USER']->id;

    //         $data = array_filter($data, function ($key) {
    //             return in_array($key, $this->allowedColumns);
    //         }, ARRAY_FILTER_USE_KEY);

    //         $this->update($_SESSION['USER']->id, $data, 'id');

    //         $response->success(true)
    //             ->message('Rental service updated successfully')
    //             ->statusCode(200)
    //             ->send();
    //     } else {
    //         $response->success(false)
    //             ->data(['errors' => $this->errors])
    //             ->message('Validation failed')
    //             ->statusCode(422)
    //             ->send();
    //     }
    // }
    
}