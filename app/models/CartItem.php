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

    public function removeCartItem($data) {

        return $this->delete($data['id'], 'id');
    }

    public function removeCartItemByCart($data) {
        return $this->delete($data['cart_id'], 'cart_id');
    }

    public function getCartItems(array $data) {
        $q = new QueryBuilder();

        $q->setTable('cart_item');
        $q->select('cart_item.*, equipment.name As e_name, equipment.image As e_image, equipment.fee As e_fee, equipment.standard_fee As e_standard_fee, equipment.description As e_description')
            ->join('item', 'cart_item.item_id', 'item.id')
            // join equipment table
            ->join('equipment', 'item.equipment_id', 'equipment.id')
            ->where('cart_item.cart_id', $data['cart_id']);
        
        return $this->query($q->getQuery(),$q->getData());
    }

    public function removeItemsByEquipmentId(array $data) {
        $q = new QueryBuilder();

        $q->setTable('cart_item');
        $q->delete('cart_item')
            ->join('item','cart_item.item_id','item.id')
            ->join('cart','cart_item.cart_id','cart.id')
            ->where('item.equipment_id', $data['equipment_id'])
            ->where('cart.customer_id', $data['customer_id']);
        // show($data);
        // show($q->getQuery());
        return $this->query($q->getQuery(), $q->getData());

    }

}