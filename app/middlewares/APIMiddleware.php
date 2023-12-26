<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

interface AuthAPIMiddlewareInterface {
    public static function run_middleware(string $controller, string $method): mixed;
    public static function is_authenticated(): void;
    public static function not_authenticated(): void;
}

class AuthAPIMiddleware {

    static $user = [];

    static $allowedColumns = ['id', 'email', 'name', 'role', 'is_verified'];

    public static function getUser(): array {
        return array_filter(self::$user, function ($key) {
            return in_array($key, self::$allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
    }

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
            'Signup' => 'ALL'
        ];

        $currentController = ucfirst($controller);

        if (isset($authRequired[$currentController]) &&
            in_array($method, $authRequired[$currentController])) {
            self::is_authenticated();
        }

        if (isset($unauthRequired[$currentController]) && ($unauthRequired[$currentController] == 'ALL' || in_array($method, $unauthRequired[$currentController]))) {
            self::not_authenticated();
        } else {
            self::checkApiToken();
        }

        return self::getUser();
    }

    private static function checkApiToken(): mixed {
        $token = null;

        // Assuming the token is sent in the Authorization header

        // show($_SERVER['HTTP_AUTHORIZATION']);

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = trim(str_replace('Bearer', '', $_SERVER['HTTP_AUTHORIZATION']));
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $token = trim(str_replace('Bearer', '', $headers['Authorization']));
            }
        }

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Token not provided']);
            exit();
        }

        try {
            $decoded = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
            $user = new UserModel;

            $data['email'] = $decoded->email;
            $userData = $user->first($data);

            if (!$userData) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid token']);
                exit();
            }

            self::$user = (array) $userData;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit();
        }

        return true;
    }

    public static function is_authenticated(): void {
        if (!self::checkApiToken()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    public static function not_authenticated(): void {
        if (self::checkApiToken()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }
}
?>
