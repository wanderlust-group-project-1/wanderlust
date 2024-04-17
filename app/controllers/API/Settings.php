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



}