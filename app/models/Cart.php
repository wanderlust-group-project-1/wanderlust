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

        return $cartItem->createCartItem(['cart_id' => $cart->id, 'item_id' => $availableItems[0]->id]);
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

        $q = "
        INSERT INTO rent (customer_id, start_date, end_date, status, total, payment_method, payment_status)
        SELECT customer_id, start_date, end_date, 'pending', SUM(equipment.price), 'cash', 'pending' FROM cart
        JOIN cart_item ON cart.id = cart_item.cart_id
        JOIN item ON cart_item.item_id = item.id
        JOIN equipment ON item.equipment_id = equipment.id
        WHERE cart.customer_id = :customer_id
        GROUP BY cart.id
        ";
        
        
       
    }







    
}