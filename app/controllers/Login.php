<?php

use Firebase\JWT\JWT;


class Login {
    use Controller;

    private function setcookie(array $userData): void{
        // print_r($userData);
        $token = JWT::encode($userData, SECRET_KEY, 'HS256');

        setcookie('jwt_auth_token', $token, time() + 36000, '/', '', false, true);

        // echo json_encode(['token' => $token]);

    }
    private function post(): mixed {
        $user = new UserModel;
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($user->authenticate($email, $password)) {
            $userData = $user->authenticate($email, $password);
            // $_SESSION['USER'] = $userData;
            // filter user data get id and email array 
            $userData = array_filter((array) $userData, function ($key) {
                return in_array($key, ['id', 'email']);
            }, ARRAY_FILTER_USE_KEY);
            $this->setcookie($userData);
            redirect('home');
        } else {
            $data['errors'] = ['email' => 'Wrong Email or Password'];
            return $data;

        }

    }


    public function index(): void {
        

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $this->post();
        }else {
            $data = [];
        }


            $this->view('login', $data);
        }
    }

?>
