<?php


Trait Model  {
    use Database;

    // protected $table = 'users';
    protected int $limit = 10;
    protected int $offset = 0;
    protected string $order_type = "desc";
    protected string $order_column = "id";
    public array $errors = [];



    



    public function findAll():array{


        $query = "select * from $this->table order by $this->order_column $this->order_type limit $this->limit offset $this->offset";

        return $this->query($query);

            // echo $query;
    // echo $query;

    }


    public function where(array $data, array $data_not = []){

        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "select * from $this->table where ";
        foreach ($keys as $key){
            $query .= $key. " = :" . $key . " && ";

        }
        foreach ($keys_not as $key){
            $query .= $key. " != :" . $key . " && ";

        }

        $query = trim($query, " && ");


        $query .= " order by $this->order_column $this->order_type limit $this->limit offset $this->offset";

        $data = array_merge($data, $data_not);
        return $this->query($query, $data);

            // echo $query;
    // echo $query;

    }
  
    public function first(array $data, array $data_not = []): mixed{

        $keys = array_keys($data);
        $keys_not = array_keys($data_not);
        $query = "select * from $this->table where ";
        foreach ($keys as $key){
            $query .= $key. " = :" . $key . " && ";

        }
        foreach ($keys_not as $key){
            $query .= $key. " != :" . $key . " && ";

        }

        $query = trim($query, " && ");


        $query .= " limit $this->limit offset $this->offset";

        $data = array_merge($data, $data_not);
        $result =  $this->query($query, $data);
        if($result)
            return $result[0];
        return false;



    }

    public function insert(array $data,array $data_not = []):int {

        // remove unwanted data 
        // if(!empty($this->allowedColumns)){
        //     foreach($data as $key => $value){
        //         if(!in_array($key,$this->allowedColumns)){
        //             unset($data[$key]);
        //         }
        //     }
        // }

        $keys = array_keys($data);
        $query = "insert into $this->table (".implode(",",$keys).") values (:".implode(",:",$keys).") ";


        
        $this->query($query,$data);
        // return 1;
        return $this->lastInsertedId();

        // return $this->lastInsertId();





    }


    public function update($id, array $data, string $id_column = 'id'):mixed {

        // remove unwanted data 


        $keys = array_keys($data);
        $query = "update $this->table set ";
        foreach ($keys as $key){
            $query .= $key. " = :" . $key . ", ";

        }

        $query = trim($query, ", ");


        $query .= " where $id_column = :$id_column ";
        $data[$id_column] = $id;

        // echo $query;
        return $this->query($query, $data);
        
    }


    public function delete($id, string $id_column = 'id'):bool{

        $data[$id_column] = $id;
        $query = "delete from $this->table where $id_column = :$id_column ";
        // echo $query;
      


        $this->query($query, $data);

        return false;
    }

    // private function lastInsertId():int{
    //     return $this->pdo->lastInsertId();
    // }

    public function lastInsertedId():mixed{

        $query = "select * from $this->table order by id desc limit 1";
        $result = $this->query($query);
        if($result)
        // return id of last inserted row

            return $result[0]->id;
    }



    // Extended functions
    protected string $q = "";

}




