<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthMiddleware {




    public static function run_middleware(string $controller, string $method): void {
         $authRequired = [
            'Home' => ['index', 'method2'],
            'Controller2' => ['method3'],
        ];
        $unauthRequired = [
            'Login' => ['index'],
            'Signup' => ['index']
        ];

        $currentController = ucfirst($controller);

        if (isset($authRequired[$currentController]) &&
            in_array($method, $authRequired[$currentController])) {
            Self::is_authenticated();
        }
        if (isset($unauthRequired[$currentController]) &&
            in_array($method, $unauthRequired[$currentController])) {
            Self::not_authenticated();
        }
        

            
    }

    private static function check():bool {
        $cookieName = 'jwt_auth_token'; 
        // print_r($_COOKIE);
        if (!isset($_COOKIE[$cookieName])) {
            setcookie('jwt_auth_token', '', time() - 1, '/');
            // redirect('login');
            return false;
        }

        $token = $_COOKIE[$cookieName];

        try {
            // echo $token;
            $decoded = JWT::decode($token, new Key( SECRET_KEY, 'HS256'));
            // The token is valid; you can access the claims as $decoded->id, $decoded->email, 

            $user = new UserModel;


            // $data = []
            // $userId = $decoded->user_id;
            $data['email'] = $decoded->email;
            // $email = $decoded->email;
            if(!$user->first($data)){
                setcookie('jwt_auth_token', '', time() - 1, '/');
                // redirect('login');
                return false;
            }
             

            //  Authorization checks

        } catch (Exception $e) {
            // Token is invalid; return an error response
            // http_response_code(401);
            // echo json_encode(['error' => 'Token is invalid']);
            // exit();
            setcookie('jwt_auth_token', '', time() - 1, '/');
            // redirect('login');
            return false;
        }
        return true;
    }
    public static function is_authenticated():void {
        if(!self::check()){
            redirect('login');
        }
    }
    public static function not_authenticated():void {
        if(self::check()){
            redirect('home');
        }
    }
}
?>
