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

    public function addItem(string $a = '', string $b = '', string $c = ''):void {
       
        $request = new JSONRequest();
        $response = new JSONResponse();

        $cart = new CartModel;

        $data = [
                'customer_id' => UserMiddleware::getUser()['id'],
                 'equipment_id' => $request->get('equipment_id'),
                 'count' => $request->get('count'),
                ];

        $result = $cart->addItemToCart($data);

        $response->data([
            'message' => 'Item added to cart',
            'status' => 'success',
            'data' => $result

        ])
        ->statusCode(200)
        ->send();


        }

        public function count(string $a = '', string $b = '', string $c = ''):void {
       
            $request = new JSONRequest();
            $response = new JSONResponse();
    
            $cart = new CartModel;
    
            $data = [
                    'customer_id' => UserMiddleware::getUser()['id'],
                ];
    
            $result = $cart->countItem($data);
    
            $response->data($result)->statusCode(200)->send();
    
    
        }

        public function removeItem(string $a = '', string $b = '', string $c = ''):void {
       
            $request = new JSONRequest();
            $response = new JSONResponse();
    
            $item = new CartItemModel;
    
            $data = [
                    'customer_id' => UserMiddleware::getUser()['id'],
                     'id' => $request->get('id'),
                ];
    
            $result = $item->removeCartItem($data);
    
            $response->data([
                'message' => 'Item removed from cart',
                'status' => 'success'   
            ])
            ->statusCode(200)
            ->send();
    
  
            }
}