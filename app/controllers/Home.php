<?php

class Home extends Controller {
    public function index($a = '',$b = ''){
        echo "This is home controller";
    }
}

$home = new Home;
// $home->index();
call_user_func_array([$home,'index'],['a' => 'abc', 'b' => 'xyz']);
?>