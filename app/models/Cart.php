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
            $cartItem = new CartItemModel;
            $cartItem->removeCartItem($cart->id, 'cart_id');

            $this->deleteCart($cart->id);










        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
        return $this->insert($data);

    }

    // insert item to cart

    public function addItemToCart(array $data){
        
        $cartItem = new CartItemModel;
        return $cartItem->createCartItem($data);
    }

    public function removeItemFromCart(array $data){
        $cartItem = new CartItemModel;
        // return $cartItem->removeCartItem($data);
    }

    //  delete cart 
    public function deleteCart(int $id){
        return $this->delete($id);
    }

    public function getCartItems(array $data) {
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







    
}