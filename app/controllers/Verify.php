<?php 

class Verify{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $token = $_GET['token'];

        $verification = new VerificationModel();
        $data = $verification->verifyToken($token);

        if ($data) {
            echo "verified";
        } else {
            echo "not verified";
        }

    }

    public function resend(string $a = '', string $b = '', string $c = ''):void {

        echo 'Please Verify your email address';
    }
}