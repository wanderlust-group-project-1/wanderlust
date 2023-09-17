<?php

class Home {

    use Controller;

    public function index($a = '',$b = '',$c =''){






        // $user = new User;
        // $arr['id'] = 1;
        // $arr['name'] = "Nimal";
        // $arr2['date'] = date("Y");
        // $result = $model->where($arr);

        // $arr['name'] = "Savinda";
        // $arr['date'] = date("Y");
        // $arr['age'] = '45';
        
        // $result = $user->insert($arr);
        // $result = $user->findAll();


        show("from the index function");

        show($a);
        show($b);
        show($c);
        // echo "This is home controller";

        $this->view('home');
    }


    public function edit($a = '', $b = '', $c = ''){
        show("from the edit function");
        show($a);
        show($b);
        show($c);
    }
}


?>