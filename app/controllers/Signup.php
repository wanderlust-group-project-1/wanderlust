<?php

class Signup {
    use Controller;

    public function index() {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new UserModel;

            $user->registerUser($_POST);
            // if ($user->validate($_POST)) {
            //     // Hash the password before inserting it into the database
            //     $_POST['password'] = $user->hashPassword($_POST['password']);
            //     $user->insert($_POST);
            //     redirect('login');
            // }

            $data['errors'] = $user->errors;
        }

        $this->view('signup', $data);
    }
}
