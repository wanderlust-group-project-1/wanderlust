<?php

class Signup {
    use Controller;

    public function index(): void {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new UserModel;

            $user->registerUser($_POST);

            $data['errors'] = $user->errors;
        }

        $this->view('signup', $data);
    }
}

?>
