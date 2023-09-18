<?php

use Firebase\JWT\JWT;


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


        // $secretKey = 'your_secret_key';

        // $userData = [
        //     'user_id' => 123,
        //     'username' => 'example_user',
        // ];

        // $token = JWT::encode($userData, $secretKey, 'HS256');

        // setcookie('jwt_token', $token, time() + 3600, '/', '', false, true);

    // echo json_encode(['token' => $token]);

            $this->view('login', $data);
        }
    }

?>
