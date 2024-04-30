<?php

class Guidestatistics {
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void {
        AuthorizationMiddleware::authorize(['guide']);
        $this->view('guide/guideStatistics');
    }

}