<?php

class Product{
    use Controller;

    public function index($a = '',$b = ''){
        echo "This is product controller";

        $this->view('product');
    }
    public function addProduct(){
        $this->view('rental/AddEquipment');
    }

    public function store(){
        // $this->view('rental/AddEquipment');
        if(isset($_POST['submit']))
        {
            $name = $_POST['name'];
            $company = $_POST['company'];
            $size = $_POST['size'];
            $colors = $_POST['colors'];
            $price = $_POST['price'];

        }

        $data = Array("name" => $name,
                      "company" => $company,
                      "size" => $size,
                      "colors" => $colors,
                      "price" => $price
        
                );
        // echo "name : " . $name . "  price : " . $price;
    }

    public function insertProduct{
        return $this->conn-> 
    }
}


?>