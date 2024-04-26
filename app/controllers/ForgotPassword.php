<?php

class ForgotPassword {
    use Controller;


    public function index(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('forgotpassword');
    }


    public function reset(string $token = ''): void
    {
        $reset = new ResetTokensModel;
        $token = $reset->first(['token' => $token]);


        if($token){
            $this->view('resetpassword', ['token' => $token->token]);
        } else {
            echo 'Invalid token';
            // redirect to forgot password page 5 seconds
            echo '<meta http-equiv="refresh" content="5;url=' . ROOT_DIR . '/forgotPassword">';
            
        }
        $this->view('resetpassword', ['token' => $token->token]);

    }
}


?>