<?php

class _404{
    use Controller;
    public function index(){
        http_response_code(404);
        
        echo "Controller not found";
    }
}


?>