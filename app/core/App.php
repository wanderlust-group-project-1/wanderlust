<?php

class APP {
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
        }else {
            require "../app/controllers/_404.php";
        }
    }

}




?>