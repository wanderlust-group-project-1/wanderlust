<?php

class Login {
    use Controller;

    public function index(): void {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new UserModel;
            $email = $_POST['email'];
            $password = $_POST['password'];

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

?>
