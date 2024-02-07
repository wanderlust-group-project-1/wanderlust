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

    public function removeCartItem($id, $column) {

        return $this->delete($id, $column);
    }

    public function getCartItems(array $data) {
        $q = new QueryBuilder();

        $q->setTable('cart_item');
        $q->select('cart_item.*, equipment.name As e_name, equipment.image As e_image, equipment.fee As e_fee ')
            ->join('item', 'cart_item.item_id', 'item.id')
            // join equipment table
            ->join('equipment', 'item.equipment_id', 'equipment.id')
            ->where('cart_item.cart_id', $data['cart_id']);
        
        return $this->query($q->getQuery(),$q->getData());
    }
}