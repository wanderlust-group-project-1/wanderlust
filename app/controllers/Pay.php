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

      

        $this->view('customer/checkout', $data);


    }

    public function payhereprocess(){

        echo "hi serverside";
 

    }
    public function notify(){

        $merchant_id         = $_POST['merchant_id'];
        $order_id            = $_POST['order_id'];
        $payhere_amount      = $_POST['payhere_amount'];
        $payhere_currency    = $_POST['payhere_currency'];
        $status_code         = $_POST['status_code'];
        $md5sig              = $_POST['md5sig'];

        $merchant_secret = MERCHANT_SECRET;

        $local_md5sig = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $payhere_amount . 
                $payhere_currency . 
                $status_code . 
                strtoupper(md5($merchant_secret)) 
            ) 
        );
               
        if (($local_md5sig === $md5sig) AND ($status_code == 2) ){
                //TODO: Update your database as payment success
        }
      

    }
}