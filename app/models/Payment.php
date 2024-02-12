<?php

class PaymentModel {
    use Model;

    protected string $table = 'payment';
    protected array $allowedColumns = [
        'id',
        'user_id',
        'amount',
        'payment_method',
        'status',
        'created_at',
        'updated_at'
    ];

    public function completePayment(array $data) {

        return $this->query('CALL PaymentComplete(:reference_number)',$data);

    }
}