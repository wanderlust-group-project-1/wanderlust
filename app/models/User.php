<?php


class UserModel {
    
    use Model;

    protected $table = 'users';
    protected $allowedColumns = [
        'email',
        'password',
    ];

    public function validate($data){
        // this->errors = [];

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
         

        if(empty($this->errors)){
            return true;

        }
        return false;

    }
}
