<?php

class Tips
{
    use Controller;

    public function index(string $a = '', string $b = '', string $c = ''): void
    {
        $this->view('admin/tips');
    }

    public function update(string $a = '', string $b = '', string $c = ''): void
    {

        $tips = new TipsModel();
        $tips->updateTips($_POST);

        redirect('profile'); 
    }
}
