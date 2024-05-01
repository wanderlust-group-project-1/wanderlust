<?php

class Message{

    public function sendMsg($msg){
        $api_instance = new NotifyLk\Api\SmsApi();
        $user_id = 1;



        echo "Message sent: $msg";

    }


}

// $msg = new Message;
// $msg->sendMsg("Hello");