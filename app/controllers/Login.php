<?php

class Login {
    use Controller;

    public function index() {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new UserModel;
            $email = $_POST['email'];
            $password = $_POST['password'];

            // $row = $user->first(['email' => $email]);

            // if ($row && $user->verifyPassword($password, $row['password'])) {
            //     $_SESSION['USER'] = $row;
            //     redirect('home');
            // } else {
            //     $data['errors'] = ['email' => 'Wrong Email or Password'];
            // }
            if ($user->authenticate($email, $password)) {
                $_SESSION['USER'] = $user->authenticate($email, $password);
                redirect('home');
            } else {
                $data['errors'] = ['email' => 'Wrong Email or Password'];
            }
        }

        $this->view('login', $data);
    }
}
