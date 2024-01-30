<?php

class GuideModel
{
    use Model;

    protected string $table = 'guides';
    protected array $allowedColumns = [
        'name',
        'address',
        'nic',
        'mobile',
        'gender',
        'user_id',
        'verification_document',
        'location_id'

    ];

    public function registerGuide(JSONRequest $request, JSONResponse $response)
    {
        $data = $request->getAll();
        $files = $_FILES; // Assuming the files are sent as part of the request
        // show($files);
        if ($this->validateGuideSignup($data, $files)) {
            $user = new UserModel;

            $data['user_id'] = $user->registerUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'guide',
            ]);

            $location = new LocationModel;
            // show($data);
            $data["location_id"] =  $location->createLocation(
                $data['latitude'],
                $data['longitude']
            );

            if ($data['user_id']) {
                // Handle file upload for verification_document or any other file
                $data['verification_document'] = upload($files['verification_document'], 'guides');
                $data = array_filter($data, function ($key) {
                    return in_array($key, $this->allowedColumns);
                }, ARRAY_FILTER_USE_KEY);

                $this->insert($data);

                $response->success(true)
                    ->data(['user_id' => $data['user_id']])
                    ->message('Guide registered successfully')
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

    public function validateGuideSignup(array $data,array $files)
    {
        $this->errors = [];

        if (empty($data['name'])) {
            $this->errors['name'] = "Name is required";
        }

        if (empty($data['address'])) {
            $this->errors['address'] = "Address is required";
        }

        if (empty($data['nic'])) {
            $this->errors['nic'] = "NIC is required";
        }

        if (empty($data['mobile'])) {
            $this->errors['mobile_number'] = "Mobile Number is required";
        }

        if (empty($data['email'])) {
            $this->errors['email'] = "Email is required";
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Email is not valid";
        }

        if (empty($files['verification_document'])) {
            $this->errors['verification_document'] = "Verification Document is required";
        }

        if (empty($data['gender'])) {
            $this->errors['gender'] = "Gender is required";
        }

        if (empty($data['password'])) {
            $this->errors['password'] = "Password is required";
        } else if (strlen($data['password']) < 6) {
            $this->errors['password'] = "Password must be at least 6 characters";
        }

        return empty($this->errors);
    }


    public function getGuide(int $id)
    {
        $q = new QueryBuilder();
        $q->setTable('guides');
        $q->select('guides.*,users.email,users.role')
            ->join('users', 'guides.user_id', 'users.id')
            ->where('guides.id', $id);

        return $this->query($q->getQuery(), $q->getData(), true);
          
    }

    public function updateStatus(JSONRequest $request, JSONResponse $response)
    {
        $data = $request->getAll();
        // show($data);
        $q = new QueryBuilder();
        $q->setTable('guides');
        $q->update(['status' => $data['newStatus']])
            ->where('id', $data['userId']);

        $this->query($q->getQuery(), $q->getData());
            $response->success(true)
                ->data(['status' => $data['newStatus']])
                ->message('Guide status updated successfully')
                ->statusCode(200)
                ->send();
        
    }


    public function updateGuide(array $data)
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
