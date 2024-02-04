<?php 

class Cart {
    use Controller;

    public function create(string $a = '', string $b = '', string $c = ''):void {
       
        $request = new JSONRequest();
        $response = new JSONResponse();

        $cart = new CartModel;

        $data = [
                'customer_id' => UserMiddleware::getUser()['id'],
                 'start_date' => $request->get('start_date'),
                 'end_date' => $request->get('end_date'),
                ];

        $result = $cart->createCart($data);

        $response->data($result)->statusCode(200)->send();


    }
}