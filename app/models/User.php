<?php


class User {
    
    use Model;

    protected $table = 'users';
    protected $allowedColumns = [
        'name',
        'age',
    ];
}
