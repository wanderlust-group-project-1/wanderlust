<?php 






class CartModel {
    use Model;

    protected string $table = 'cart';

    
    protected array $allowedColumns = [
        'customer_id',
        'start_date',
        'end_date',
    ];

    public function createCart(array $data){



        // if cart already exists for customer, delete it

        $cart = $this->first(['customer_id' => $data['customer_id']]);
        // show($cart);
        // if ($cart) {

        //     $cartItem = new CartItemModel;
        //     $cartItem->removeCartItem($cart[0]->id, 'cart_id');

        //     $this->deleteCart(['id' => $cart[0]->id]);
        // }

            // forEach item in cart, remove it
            // show($cart);
            // show("s");
            if ($cart) {
                $cartItem = new CartItemModel;
                $cartItem->removeCartItemByCart(['cart_id' => $cart->id]);

                $this->deleteCart($cart->id);
            }
            // $cartItem = new CartItemModel;
            // $cartItem->removeCartItem($cart->id, 'cart_id');

            // $this->deleteCart($cart->id);










        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
        return $this->insert($data);

    }

    // insert item to cart

    public function addItemToCart(array $data){
        
        $cartItem = new CartItemModel;

        $cart = $this->first(['customer_id' => $data['customer_id']]);
        $data['cart_id'] = $cart->id;
        $data['start_date'] = $cart->start_date;
        $data['end_date'] = $cart->end_date;

        $item = new ItemModel;
        $availableItems = $item->getAvailableItems($data);
        // show($availableItems);

        // return $cartItem->createCartItem(['cart_id' => $cart->id, 'item_id' => $availableItems[0]->id]);
        while ($data['count'] > 0) {
            $cartItem->createCartItem(['cart_id' => $cart->id, 'item_id' => $availableItems[$data['count'] - 1]->id]);
            $data['count']--;
        }
        
        return true;

    }

    public function removeItemFromCart(array $data){
        $cartItem = new CartItemModel;
        
        
    }

    //  delete cart 
    public function deleteCart(int $id){
        return $this->delete($id);
    }

    public function getCartItems(array $data) {

        $cart = $this->first($data);

        if (!$cart) {
            return false;
        }

        $data['cart_id'] = $cart->id;
   

        $cartItem = new CartItemModel;
        return $cartItem->getCartItems($data);
    }


    public function getCart(array $data) {
        $q = new QueryBuilder();

        

        $q->setTable('cart');
        $q->select('cart.*, cart_item.item_id As item_id')
            ->join('cart_item', 'cart.id', 'cart_item.cart_id')
            ->where('cart.customer_id', $data['customer_id']);

        
        return $this->query($q->getQuery(),$q->getData());
    }


    public function countItem(array $data) {

        $q = new QueryBuilder();

        $q->setTable('cart');
        $q->count('cart_item.item_id')
            ->join('cart_item', 'cart.id', 'cart_item.cart_id')
            ->where('cart.customer_id', $data['customer_id']);


        return $this->query($q->getQuery(),$q->getData())[0]->count;

  
    }


    public function payCart (array $data) {

        // call stored procedure to pay cart


        // return $this->query("CALL CompleteRentProcess(:customer_id)", $data)[0];
        return $this->query("CALL ProcessCartToRentOrders(:customer_id)", $data)[0];

        
        
       
    }







    
}