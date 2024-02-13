<?php

class Cart {
    use Controller;


    private function amount($data){
        $total = 0;
        foreach ($data as $item) {
            
            $total += $item->e_fee;
        }
        return $total;

    }


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

    public function checkout(string $a = '', string $b = '', string $c = ''):void {
        $cart = new CartModel;
        $data = [
                'customer_id' => UserMiddleware::getUser()['id'],
            ];
        $data['items'] = $cart->getCartItems($data);

        $data['amount'] = $this->amount($data['items']);
        $data['order_id'] = '33535';

        $merchant_id = MERCHANT_ID;
        $merchant_secret = MERCHANT_SECRET;

        // show($merchant_secret);
        

        $hash = strtoupper(
            md5(
                $merchant_id . 
                $data['order_id'] . 
                number_format($data['amount'], 2, '.', '') .
                'LKR' .  
                strtoupper(md5($merchant_secret))
            ) 
        );

        $data['hash'] = $hash;
        $data['merchant_id'] = $merchant_id;




        // show($data);
        $this->view('customer/checkout', $data);
    }


    


    
}