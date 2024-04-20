<?php

use Firebase\JWT\JWT;


class Login {
    use Controller;

    private function setcookie(array $userData): void{
        // print_r($userData);
        $token = JWT::encode($userData, SECRET_KEY, 'HS256');

        setcookie('jwt_auth_token', $token, time() + 36000, '/', '', false, false);

        // echo json_encode(['token' => $token]);

    }
    public function index(): void {
        $user = new UserModel;
        // $email = $_POST['email'];
        // $password = $_POST['password'];
        // get from json body

        $request = new JSONRequest;
        $email = $request->get('email');
        $password = $request->get('password');



        // $data = json_decode(file_get_contents('php://input'), true);
        // $email = $data['email'];
        // $password = $data['password'];

        $response = new JSONResponse;
        if ($user->authenticate($email, $password)) {
            $userData = $user->authenticate($email, $password);
            // $_SESSION['USER'] = $userData;

            // Check if user is verified
            // show($userData);
            // die();
            if ($userData->is_verified == 0) {
                $response->success(false)->message('User not verified, please verify your email')->statusCode(401)->send();
                return;
            }
            // filter user data get id and email array 
            $userCookie = array_filter((array) $userData, function ($key) {
                return in_array($key, ['id', 'email']);
            }, ARRAY_FILTER_USE_KEY);
            $this->setcookie($userCookie);
            $response->success(true)->data($userData)->send();

        } else {

            $response->success(false)->message('Wrong Email or Password')->statusCode(401)->send();


        }

    }



    }

?>
