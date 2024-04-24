<?php

class Test{
    use Controller;

    public function index(){
        $msg = new Message;
        $msg->sendMsg("Hello");
    }   
}

?>