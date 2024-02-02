<?php

class CartItemModel {
    use Model;

    protected string $table = 'cart_item';
    protected array $allowedColumns = [
        'cart_id',
        'item_id',
    ];

    
    public function createCartItem(array $data){
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
        return $this->insert($data);

    }

    public function removeCartItem(array $data) {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->delete($data);
    }

    public function getCartItems(array $data) {
        $q = new QueryBuilder();

        $q->setTable('cart_item');
        $q->select('cart_item.*, item.name As item_name')
            ->join('item', 'cart_item.item_id', 'item.id')
            ->where('cart_item.cart_id', $data['cart_id']);
        
        return $this->query($q->getQuery(),$q->getData());
    }
}