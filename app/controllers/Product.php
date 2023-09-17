<?php

class Product{
    use Controller;

    public function index($a = '',$b = ''){
        echo "This is product controller";

        $this->view('product');
    }
}


?>