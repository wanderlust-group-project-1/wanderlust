<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthMiddleware {

    static $user = [];

    static $allowedColumns = ['id', 'email', 'name', 'role'];

    // filter user with allowed columns

    public static function getUser(): array {
        return array_filter(Self::$user, function ($key) {
            return in_array($key, Self::$allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
    }



    // protected static $user;

    public static function run_middleware(string $controller, string $method): mixed {
         $authRequired = [
            // 'Home' => ['index', 'method2'],
            'Controller2' => ['method3'],
            'Customer' => ['index', 'edit', 'update'],
            'Profile' => ['index', 'edit', 'update'],
            // 'Profile' => ['index', 'edit', 'update'],
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
        }else {
            Self::check();
        }
        
        // return Self::$user;
        return Self::getUser();

            
    }

    private static function check():mixed {
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

            // show($user);
            // $data = []
            // $userId = $decoded->user_id;
            $data['email'] = $decoded->email;
            // $email = $decoded->email;
            // show($user->first($data));

            $userData = $user->first($data);
            if(!$userData){
                setcookie('jwt_auth_token', '', time() - 1, '/');
                // redirect('login');
                return false;
            }
            // return $userData;
            // std class to array
            // $this->$user = (array) $userData;



            Self::$user = (array) $userData;

             

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
