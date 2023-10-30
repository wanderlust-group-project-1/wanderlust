<?php

class Logout {
    use Controller;

    public function index(): void {
        $data['email'] = empty($_SESSION['USER']) ? '' : $_SESSION['USER']->email;
        
        setcookie('jwt_auth_token', '', time() - 1, '/');
        session_destroy();
        redirect('home');
    }
}

?>
