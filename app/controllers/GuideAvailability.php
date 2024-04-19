<?php

class GuideAvailability{
    use Controller;
    public function index(string $a = '', string $b = '', string $c = ''): void {
        $this->view('guide/guideAvailability');
    }
}