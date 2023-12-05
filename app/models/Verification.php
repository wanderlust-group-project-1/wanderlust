<?php

class VerificationModel {
    use Model;

    protected string $table = 'verification';
    protected array $allowedColumns = [
        'user_id',
        'token'
    ];

    public function generateToken(int $user_id): string {
        $token = bin2hex(random_bytes(32));
        $this->insert([
            'user_id' => $user_id,
            'token' => $token
        ]);
        return $token;
    }

    public function verifyToken(string $token): mixed {
        $data = $this->first(['token' => $token]);
        if ($data) {
            $this->delete( $token, 'token');
            $user = new UserModel;
            // $user->updateUser(['id' => $data->user_id,'verified' => 1]);
            $user->verifyUser($data->user_id);
            return $data;
        }
        return false;
    }

}