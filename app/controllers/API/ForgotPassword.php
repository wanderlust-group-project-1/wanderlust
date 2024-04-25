<?php

class ForgotPassword {
    use Controller;


    public function email(string $a = '', string $b = '', string $c = ''): void
    {
        $request = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();

        $user = new UserModel;
        $user = $user->first(['email' => $data['email']]);

        $reset = new ResetTokensModel;

        if($user){
            $token = bin2hex(random_bytes(32));
            $reset->insert([
                'user_id' => $user->id,
                'token' => $token
            ]);

            $link = ROOT_DIR . 'forgotPassword/reset/' . $token;

            $emailSender = new EmailSender();
            $emailSender->sendEmail($data['email'], 'Reset Password', 'Click the link below to reset your password: <a href="' . $link . '">Reset Password</a>');

            $response->success(true)
                ->message('Email sent successfully')
                ->statusCode(200)
                ->send();
        } else {
            $response->success(false)
                ->message('Email not found')
                ->statusCode(404)
                ->send();
        }
       
    }
}


?>