<?php

class RentPayModel {
    use Model;

    protected string $table = 'rent_pay';

    
    protected array $allowedColumns = [
        'rent_id',
        'payment_id',
    ];

    public function createRentPay(array $data){
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);
        return $this->insert($data);
    }

    public function getRentPay(array $data){
        return $this->first($data);
    }

    public function getRentPays(array $data){
        return $this->where($data);
    }

    public function updateRentPay(array $data, array $condition){
        return $this->update($data, $condition);
    }

    public function deleteRentPay(array $data){
        return $this->delete($data);
    }
}