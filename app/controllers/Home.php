<?php

class Home extends Controller {
    public function index($a = '',$b = ''){

        $model = new Model;
        // $arr['id'] = 1;
        // $arr['name'] = "nirmal";
        // $arr2['date'] = date("Y");
        // $result = $model->where($arr);

        // $arr['name'] = "Savinda";
        // $arr['date'] = date("Y");
        // $arr['age'] = '19';
        
        $result = $model->delete(3);


        show($result);
        echo "This is home controller";

        $this->view('home');
    }
}


?>