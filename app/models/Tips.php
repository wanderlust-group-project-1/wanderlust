<?php

class TipsModel
{
    use Model;

    protected string $table = 'tips';
    protected array $allowedColumns = [
        //'id',
        'title',
        'description',
        'author',
        'id',
    ];

    public function validateCustomerSignup(array $data)
    {
        $this->errors = [];

        if (empty($data['title'])) {
            $this->errors['title'] = "Title is required";
        }

        if (empty($data['description'])) {
            $this->errors['description'] = "Description is required";
        }
        return empty($this->errors);
    }

    public function updateTip(array $data)
    {

        // $user = new UserModel;

        // $data['id'] = $_SESSION['USER']->id;
        $id = $data['id'];
        // alowed column
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        // show($data);
        return $this->update($id, $data, 'id');
    }

    public function addTip(array $data)
    {

        // $user = new UserModel;

        // $data['id'] = $_SESSION['USER']->id;
        $data['author'] = "admin";

        // alowed column
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->insert($data);
    }

    public function findAll(): array
    {
        $data['author'] = "admin";
        return $this->where($data);
        return false;
    }
}
