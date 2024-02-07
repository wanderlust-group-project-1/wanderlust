<?php

class Cart {
    use Controller;


    public function viewCart(string $a = '', string $b = '', string $c = ''):void {


        $cart = new CartModel;
        $data = [
                'customer_id' => UserMiddleware::getUser()['id'],
            ];
        $data = $cart->getCartItems($data);
        // show($data);
        $this->view('customer/components/cart', $data); 
    //    echo  "view cart";

    }

    
}