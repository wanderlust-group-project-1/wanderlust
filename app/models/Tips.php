<?php

class TipsModel
{
    use Model;

    protected string $table = 'tips';
    protected array $allowedColumns = [
        //'id',
        'title',
        'description',
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

    public function updateTips(array $data)
    {

        // $user = new UserModel;

        $data['id'] = $_SESSION['USER']->id;

        // alowed column
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->update($_SESSION['USER']->id, $data, 'id');
    }
}
