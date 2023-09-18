<?php

class UserModel {
    
    use Model;

    protected $table = 'users';
    protected $allowedColumns = [
        'email',
        'password',
    ];

    // Update the hashing algorithm and salt length as needed
    private $hashAlgorithm = "sha256";
    private $saltLength = 16;

    public function registerUser($data){
        if ($this->validate($data)) {
            $data['password'] = $this->hashPassword($data['password']);
            return $this->insert($data);
        }
        return false;
    }

    public function authenticate($email, $password){
        $data = $this->first(['email' => $email]);
        if ($data && $this->verifyPassword($password, $data->password)) {
            return $data;
        }
        return false;
    }

    private function hashPassword($password){
        $salt = random_bytes($this->saltLength);
        $hashedPassword = hash_pbkdf2($this->hashAlgorithm, $password, $salt, 10000, 64);
        return base64_encode($salt) . ":" . $hashedPassword;
    }

    private function verifyPassword($password, $hashedPassword){
        list($salt, $hash) = explode(":", $hashedPassword);
        $computedHash = hash_pbkdf2($this->hashAlgorithm, $password, base64_decode($salt), 10000, 64);
        return hash_equals($hash, $computedHash);
    }

    public function validate($data){
        $this->errors = [];

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
}
