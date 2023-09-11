<?php

class APP {

    private $controller = 'Home';
    private $method = 'index';


    private function splitURL(){
        $URL = $_GET['url'] ?? 'home';
        $URL = explode("/",$URL);
        return $URL;
    }
    
    public function loadController(){
        $URL = $this->splitURL();
        // echo ucfirst($URL[0]);
    
        $filename= "../app/controllers/".ucfirst($URL[0]).".php";
        // echo $filename;
        if(file_exists($filename)){
            require $filename;
            $this->controller = ucfirst($URL[0]);
        }else {
            require "../app/controllers/_404.php";
            $this->controller = "_404";

        }

        $controller = new $this->controller;
        // $home->index();
        call_user_func_array([$controller,$this->method],[]);
    }

}




?>