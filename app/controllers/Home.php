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


        // show("from the index function");

        // echo "This is home controller";

        $data['email'] = empty($_SESSION['USER']) ? 'Guest' : $_SESSION['USER']->email;

        $this->view('home',$data);
    }


    public function edit($a = '', $b = '', $c = ''){
        show("from the edit function");

    }
}


?>