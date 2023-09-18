<?php

class UserModel {
    
    use Model;

    protected string $table = 'users';
    protected array $allowedColumns = [
        'email',
        'password',
    ];

    // Update the hashing algorithm and salt length as needed
    private string $hashAlgorithm = "sha256";
    private int $saltLength = 16;

    public function registerUser(array $data){
        if ($this->validate($data)) {
            $data['password'] = $this->hashPassword($data['password']);
            return $this->insert($data);
        }
        return false;
    }

    public function authenticate(string $email,string $password):mixed {
        $data = $this->first(['email' => $email]);
        if ($data && $this->verifyPassword($password, $data->password)) {
            return $data;
        }
        return false;
    }

    private function hashPassword(string $password): string {
        $salt = random_bytes($this->saltLength);
        $hashedPassword = hash_pbkdf2($this->hashAlgorithm, $password, $salt, 10000, 64);
        return base64_encode($salt) . ":" . $hashedPassword;
    }

    private function verifyPassword(string $password, string $hashedPassword): bool{
        list($salt, $hash) = explode(":", $hashedPassword);
        $computedHash = hash_pbkdf2($this->hashAlgorithm, $password, base64_decode($salt), 10000, 64);
        return hash_equals($hash, $computedHash);
    }

    public function validate(array $data){
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
