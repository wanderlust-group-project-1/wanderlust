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
}



class ModelExtended
{
    use DatabaseExtended;

    protected string $table = "";
    protected array $columns = [];
    protected array $errors = [];
    protected int $nrows = 1000;

    /**
     * Select rows from table
     * @param array|string $column_list
     * @return Model
     */
    public function select(array|string $column_list = "*"): ModelExtended
    {
        if (empty($column_list)) {
            $column_list = "*";
        }
        if (is_array($column_list)) {
            for ($i = 0; $i < count($column_list); $i++) {
                if (!str_contains($column_list[$i], ".")) {
                    $column_list[$i] = $this->table . "." . $column_list[$i];
                }
            }
            $column_list = implode(", ", $column_list);
        } else {
            if (!str_contains($column_list, ".")) {
                $column_list = $this->table . "." . $column_list;
            }
        }

        $this->query = "SELECT $column_list FROM $this->table";
        return $this;
    }

    public function count(string $column = '*'): ModelExtended
    {
        $this->query = "SELECT COUNT($column) FROM $this->table";
        return $this;
    }

    // Get number of pages according to the offset
    public function getPages(): int
    {
        $this->query = "SELECT COUNT(*) FROM $this->table";
        $c = $this->fetch();
        return ceil($c->{'COUNT(*)'}/$this->nrows);
    }

    /**
     * Insert a row into table
     * @param array $data
     * @return void
     */
    public function insert(array $data): void
    {
        foreach (array_keys($data) as $column) {
            if (!in_array($column, $this->columns)) {
                unset($data[$column]);
            }
        }
        if (empty($data)) {
            return;
        }
        $column_list = implode(", ", array_keys($data));
        $value_list = "";
        foreach ($data as $ignored) {
            $value_list .= "?, ";
        }
        $value_list = rtrim($value_list, ", ");
        $this->query = "INSERT INTO $this->table ($column_list) VALUES ($value_list)";
        $this->data = array_values($data);
        $this->execute();
    }


    public function update(array $data): ModelExtended
    {
        $column_list = "";
        foreach (array_keys($data) as $column) {
            if (!in_array($column, $this->columns)) {
                unset($data[$column]);
            }
            $column_list .= "$column = ?, ";
        }
        if (empty($data)) {
            return $this;
        }
        $column_list = rtrim($column_list, ", ");
        $this->query = "UPDATE $this->table SET $column_list";
        $this->data = array_values($data);
        return $this;
    }


    public function delete(): ModelExtended
    {
        $this->query = "DELETE FROM $this->table";
        return $this;
    }


    public function where(string $column, string $value, string $operator = "="): ModelExtended
    {
        $this->query .= " WHERE $column $operator ?";
        $this->data[] = $value;
        return $this;
    }


    public function wherecolumn(string $column1, string $column2, string $operator = "="): ModelExtended
    {
        $this->query .= " WHERE $column1 $operator $column2";
        return $this;
    }


    public function and(string $column, string $value, string $operator = "="): ModelExtended
    {
        $this->query .= " AND $column $operator ?";
        $this->data[] = $value;
        return $this;
    }
    //Check for null values
    //Usage: $this->checkNull("AND", "column", "value", "IS NOT");
    //Usage: $this->checkNull("OR", "column", "value");
    public function checkNull(string $operation,string $column, string $operator = "IS"): ModelExtended
    {
        $this->query .= " $operation $column $operator NULL";
        return $this;
    }

    /**
     * Or clause
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return Model
     */
    public function or(string $column, string $value, string $operator = "="): ModelExtended
    {
        $this->query .= " OR $column $operator ?";
        $this->data[] = $value;
        return $this;
    }

    /**
     * Order by clause
     * @param string $column
     * @param string $direction
     * @return Model
     */
    public function orderBy(string $column, string $direction = "ASC"): ModelExtended
    {
        if (empty($column)) {
            return $this;
        }
        $this->query .= " ORDER BY $column $direction";
        return $this;
    }

    /**
     * Limit clause
     * @param int $limit
     * @return Model
     */
    public function limit(int $limit): ModelExtended
    {
        if ($limit != 0) {
            $this->query .= " LIMIT $limit";
        }
        return $this;
    }

    /**
     * Offset clause
     * @param int $offset
     * @return Model
     */
    public function offset(int $offset): ModelExtended
    {
        $this->query .= " OFFSET $offset";
        return $this;
    }

    
    /**
     * Join clause
     */
    public function join(string $table, string $column1, string $column2, string $operator = "="): ModelExtended
    {
        $this->query .= " JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    /**
     * Left join clause
     */

    public function leftJoin(string $table, string $column1, string $column2, string $operator = "="): ModelExtended
    {
        $this->query .= " LEFT JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    /**
     * Right join clause
     */

    public function rightJoin(string $table, string $column1, string $column2, string $operator = "="): ModelExtended
    {
        $this->query .= " RIGHT JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    public function contains(array $columns, string $value): ModelExtended
    {
        $this->query .= " WHERE (";
        foreach ($columns as $column) {
            $this->query .= $column . " LIKE ? OR ";
            $this->data[] = "%$value%";
        }
        $this->query = rtrim($this->query, "OR ");
        $this->query .= ")";
        return $this;
    }

    /**
     * Return errors
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Return last insert id
     * @return int
     */
    public function lastInsertId(): int
    {
        return $this->db->lastInsertId();
    }

    public function concat(array $columns, string $separator, $as = null): string
    {
        $query = " CONCAT(";
        foreach ($columns as $column) {
            $query .= $column . ", '$separator', ";
        }
        $query = rtrim($query, ", '$separator', ");
        $query .= ")";
        if ($as) {
            $query .= " AS $as";
        }
        return $query;
    }

    public function min($column): ModelExtended
    {
        $this->query = "SELECT MIN($column) AS $column FROM $this->table";
        return $this;
    }

    public function max($column): ModelExtended
    {
        $this->query = "SELECT MAX($column) AS $column FROM $this->table";
        return $this;
    }
}
