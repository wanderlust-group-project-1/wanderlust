<?php

class Settings {
    use Controller;

    public function renting(string $a = '', string $b = '', string $c = ''):void {

        $request = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();
        

        $id = UserMiddleware::getUser()['id'];

        $rentSettings = new RentalSettingsModel;
        $re =  $rentSettings->updateSettings($id, $data);

        $response->statusCode(200)->data($re)->send();
        

       
        

    }

    public function emailChange(string $a = '', string $b = '', string $c = ''):void {

        $request = new JSONRequest;
        $response = new JSONResponse;

        $data = $request->getAll();
        

        $id = UserMiddleware::getUser()['user_id'];

        $verification = new VerificationModel;
        $token = $verification->generateToken($id, $data['email']);


        try {
            $email = new EmailSender();
            $email->sendEmail($data['email'], "Verify your email", "Click on the link to verify your email: http://localhost:8000/verify?token=$token");  
            

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        $response->statusCode(200)->message("Email verification link sent to your email")->send();


      


    }



}