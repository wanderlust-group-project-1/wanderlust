<?php

class GuideModel {
    use Model;

    protected string $table = 'guides';
    protected array $allowedColumns = [
        'name',
        'address',
        'nic',
        'mobile',
        'gender',
        'user_id',
    ];

    public function registerGuide(array $data) {
        if ($this->validateGuideSignup($data)) {
            $user = new UserModel;

            $data['user_id'] = $user->registerUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'guide',
            ]);

            if ($data['user_id']) {
                $data = array_filter($data, function ($key) {
                    return in_array($key, $this->allowedColumns);
                }, ARRAY_FILTER_USE_KEY);

                return $this->insert($data);
            }
        }

        return false;
    }

    public function validateGuideSignup(array $data) {
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
}
