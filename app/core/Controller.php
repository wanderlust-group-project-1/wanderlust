<?php

Trait Controller{

    public function view($name, $data = []){

        if(!empty($data)){
            extract($data);
        }
        
        $filename= "../app/views/".$name.".view.php";
        // echo $filename;
        if(file_exists($filename)){
            require $filename;
        }else {
            require "../app/views/404.view.php";
        }
    }
}