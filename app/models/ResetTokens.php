<?php

class ResetTokensModel {
    use Model;

    protected string $table = 'reset_tokens';

    protected array $allowedColumns = [
        'user_id',
        'token',
    ];

    
}