<?php

class One {
    use Controller;
    public function index($a = '',$b = ''){
        echo "This is one controller";

        $this->view('1/one');
    }
}


?>