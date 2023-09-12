<?php

class Home extends Controller {
    public function index($a = '',$b = ''){

        $model = new Model;
        $result = $model->where(['id' => 1]);


        show($result);
        echo "This is home controller";

        $this->view('home');
    }
}


?>