<?php

class Logout {
    use Controller;

    public function index(): void {
        $data['email'] = empty($_SESSION['USER']) ? '' : $_SESSION['USER']->email;
        
        session_destroy();
        redirect('home');
    }
}

?>
