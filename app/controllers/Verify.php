<?php 

class Verify{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''):void {
        $token = $_GET['token'];

        $verification = new VerificationModel();
        $data = $verification->verifyToken($token);

        if ($data) {
            echo "verified";
            // redirect to login page 
            echo "<script>window.location.href = '".ROOT_DIR."/login';</script>";
        } else {
            echo "not verified";

            // redeirect with timeout 
            echo "<script>setTimeout(function(){ window.location.href = '".ROOT_DIR."/login'; }, 2000);</script>";
        }

    }

    public function resend(string $a = '', string $b = '', string $c = ''):void {

        echo 'Please Verify your email address';
    }
}