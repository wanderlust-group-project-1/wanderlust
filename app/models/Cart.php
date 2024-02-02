<?php 



// //CREATE TABLE Cart (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     customer_id INT NOT NULL,
//     start_day DATE NOT NULL,
//     end_day DATE,
//     FOREIGN KEY (customer_id) REFERENCES Customer(id) -- Assumes a Customer table exists
// );

// CREATE TABLE CartItem (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     cart_id INT NOT NULL,
//     item_id INT NOT NULL,
//     FOREIGN KEY (cart_id) REFERENCES Cart(id),
//     FOREIGN KEY (item_id) REFERENCES Item(id) -- Assumes an Item table exists
// );


class CartModel {
    use Model;

    protected string $table = 'cart';

    
    protected array $allowedColumns = [
        'customer_id',
        'start_date',
        'end_date',
    ];

    public function createCart(array $data){
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
        return $cartItem->removeCartItem($data);
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