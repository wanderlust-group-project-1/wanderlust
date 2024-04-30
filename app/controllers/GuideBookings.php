<?php

class GuideBookings{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
        AuthorizationMiddleware::authorize(['guide']);
        $this->view('guide/guidebooking');
    }
}