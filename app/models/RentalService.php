<?php

class RentalServicesModel extends UserModel {
    use Model;

    protected string $table = 'rental_services';
    protected array $allowedColumns = [
        'name',
        'address',
        'regNo',
        'mobile',
        'email',
        'password',
    ];


}