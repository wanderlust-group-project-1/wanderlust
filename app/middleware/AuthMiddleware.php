<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthMiddleware {

    public function handle():void {
        $cookieName = 'jwt_auth_token'; 
        // print_r($_COOKIE);
        if (!isset($_COOKIE[$cookieName])) {
            setcookie('jwt_auth_token', '', time() - 1, '/');
            redirect('login');
        }

        $token = $_COOKIE[$cookieName];

        try {
            // echo $token;
            $decoded = JWT::decode($token, new Key( SECRET_KEY, 'HS256'));
            // The token is valid; you can access the claims as $decoded->id, $decoded->email, etc.

            $user = new UserModel;


            // $data = []
            // $userId = $decoded->user_id;
            $data['email'] = $decoded->email;
            // $email = $decoded->email;
            if(!$user->first($data)){
                setcookie('jwt_auth_token', '', time() - 1, '/');
                redirect('login');
            }
             

            //  Authorization checks

        } catch (Exception $e) {
            // Token is invalid; return an error response
            // http_response_code(401);
            // echo json_encode(['error' => 'Token is invalid']);
            // exit();
            setcookie('jwt_auth_token', '', time() - 1, '/');
            redirect('login');
        }
    }
}
?>
