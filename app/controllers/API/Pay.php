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


        $order = $cart->payCart($data);

        // show($order);

        $merchant_id = MERCHANT_ID;
        $merchant_secret = MERCHANT_SECRET;

        $hash = strtoupper(
            md5(
                $merchant_id . 
                $order->orderID . 
                // number_format($order->totalAmount, 2, '.', '') .
                $order->totalAmount .
                'LKR' .  
                strtoupper(md5($merchant_secret))
            ) 
        );
        
        $data['hash'] = $hash;
        $data['merchant_id'] = $merchant_id;
        $data['orderId'] = $order->orderID;
        $data['amount'] = $order->totalAmount;

        $response->data($data)->statusCode(200)->send();



        // // show($data);




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
                    strtoupper(md5($merchant_secret)) 
                ) 
            );
                
            if (($local_md5sig === $md5sig) AND ($status_code == 2) ){

                $payment = new PaymentModel;
                $data = $payment->completePayment(['reference_number' =>  $order_id ]);


                
                show("Transaction Successful");

                

                


                

            }
            else{
                show("Invalid Transaction");
            }

            // show($_POST);
            // show($local_md5sig);
            // show($md5sig);
            // show($status_code);

    }

}



?>