<?php

class Pay {
    use Controller;

    public function cart(string $a = '', string $b = '', string $c = ''):void {
        $request = new JSONRequest();
        $response = new JSONResponse();

        $cart = new CartModel;
        $data = [
                'customer_id' => UserMiddleware::getUser()['id'],
            ];

            
        $data = $cart->getCartItems($data);

        




        // // show($data);


    }
}



?>