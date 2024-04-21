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
        $c = [
                'customer_id' => UserMiddleware::getUser()['id'],
            ];
        
        $data['items'] = $cart->getCartItems($c);
        $data['cart'] = $cart->first($c);




        foreach ($data['items'] as $equipment) {
            $equipment->total = $equipment->e_standard_fee + $equipment->e_fee * (strtotime($data['cart']->end_date) - strtotime($data['cart']->start_date)) / (60 * 60 * 24);
        }

        // show($data);
        // calculate the total of all items

        $data['total'] = 0;

        foreach ($data['items'] as $item) {
            $data['total'] += $item->total;
        }


    
        $this->view('customer/components/cart', $data); 
    //    echo  "view cart";

    }

    public function checkout(string $a = '', string $b = '', string $c = ''):void {
        $cart = new CartModel;
        $c = [
                'customer_id' => UserMiddleware::getUser()['id'],
            ];
        $data['items'] = $cart->getCartItems($c);


        if(!$data['items']){
            redirect('rent');
            return;
        }
        
        $data['cart'] = $cart->first($c);

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



        foreach ($data['items'] as $equipment) {
            $equipment->total = $equipment->e_standard_fee + $equipment->e_fee * (strtotime($data['cart']->end_date) - strtotime($data['cart']->start_date)) / (60 * 60 * 24);
        }

        $data['total'] = 0;

        foreach ($data['items'] as $item) {
            $data['total'] += $item->total;
        }



        // show($data);
        $this->view('customer/checkout', $data);
    }


    


    
}