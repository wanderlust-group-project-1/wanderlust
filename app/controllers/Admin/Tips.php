<?php

class Tips
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {

        $tips = new TipsModel();
        $data['tips'] = $tips->findAll();
        // show($data);
        $this->view('admin/tips', $data);
    }

    public function update(string $a = '', string $b = '', string $c = ''): void
    {

        $tips = new TipsModel();
        $tips->updateTip($_POST);

        redirect('admin/tips'); 
    }

    public function add(string $a = '', string $b = '', string $c = ''): void
    {

        $tips = new TipsModel();
        $tips->addTip($_POST);
        // show($tips);

        redirect('admin/tips'); 
    }

    public function delete(string $a = '', string $b = '', string $c = ''): void
    {

        // show($a);
        // show($b);
        // show($c);
        $tips = new TipsModel();
        // show($tips->first(['id' => $a]));

        $tips->delete($a, 'id');
        

        redirect('admin/tips'); 
    }
}