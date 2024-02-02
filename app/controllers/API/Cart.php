<?php 

class Cart {
    use Controller;

    public function create(string $a = '', string $b = '', string $c = ''):void {
       
        $request = new JSONRequest();
        $response = new JSONResponse();

        $rent = new CartModel;

        $data = ['user_id' => UserMiddleware::getUser()['id']];

        $response->data($data)->statusCode(200)->send();


    }
}