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
    public function index(): void {
        $user = new UserModel;
        // $email = $_POST['email'];
        // $password = $_POST['password'];
        // get from json body
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $password = $data['password'];

        if ($user->authenticate($email, $password)) {
            $userData = $user->authenticate($email, $password);
            // $_SESSION['USER'] = $userData;
            // filter user data get id and email array 
            $userData = array_filter((array) $userData, function ($key) {
                return in_array($key, ['id', 'email']);
            }, ARRAY_FILTER_USE_KEY);
            $this->setcookie($userData);
            header('Content-Type: application/json');
            echo json_encode(['success' => 'true']);
            // redirect('home');
        } else {
            $data['errors'] = ['email' => 'Wrong Email or Password'];
            // return $data;
            header('Content-Type: application/json');
            // status 401
            http_response_code(401);
            echo json_encode($data);

        }

    }



    }

?>
