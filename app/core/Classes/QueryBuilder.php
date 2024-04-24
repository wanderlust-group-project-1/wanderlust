<?php

class QueryBuilder
{
    private string $table = "";
    private array $columns = [];
    private array $errors = [];
    private int $nrows = 1000;
    private string $query = "";
    private array $data = [];

    private bool $isWhere = false;

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array|string $column_list = "*")
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

    public function update(array $data): self
    {
        $this->query = "UPDATE $this->table SET ";
        $this->data = array_values($data);
        $this->columns = array_keys($data);
        $this->query .= implode(" = ?, ", $this->columns) . " = ?";
        return $this;
    }

    public function delete(): self
    {
        $this->query = "DELETE FROM $this->table";
        return $this;
    }



    public function count(string $column = '*')
    {
        $this->query = "SELECT COUNT($column) AS count FROM $this->table";
        return $this;
    }

    public function where(string $column, string $value, string $operator = "="): self
    {
        if (!$this->isWhere) {
            $this->query .= " WHERE $column $operator ?";
            $this->isWhere = true;
        } else {
            $this->query .= " AND $column $operator ?";
        }
        // $this->query .= " WHERE $column $operator ?";
        $this->data[] = $value;
        return $this;
    }

    // public function orWhere(string $column, string $value, string $operator = "="): self
    // value can be null
    public function orWhere(string $column,  $value, string $operator = "="): self
    {
        if (!$this->isWhere) {
            $this->query .= " WHERE $column $operator ?";
            $this->isWhere = true;
        } else {
            $this->query .= " OR $column $operator ?";
        }
        // $this->query .= " WHERE $column $operator ?";
        $this->data[] = $value;
        return $this;
    }

    public function join(string $table, string $column1, string $column2, string $operator = "="): self
    {
        $this->query .= " INNER JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    public function leftJoin(string $table, string $column1, string $column2, string $operator = "="): self
    {
        $this->query .= " LEFT JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    public function rightJoin(string $table, string $column1, string $column2, string $operator = "="): self
    {
        $this->query .= " RIGHT JOIN $table ON $column1 $operator $column2";
        return $this;
    }

    // Add other query builder methods...

    public function groupBy(string $column): self
    {
        $this->query .= " GROUP BY $column";
        return $this;
    }

    public function orderBy(string $column, string $order = "ASC"): self
    {
        $this->query .= " ORDER BY $column $order";
        return $this;
    }


    public function append(string $query): self
    {
        $this->query .= " $query";
        return $this;
    }

    // data append 
    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }



    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function reset(): self
    {
        $this->table = "";
        $this->columns = [];
        $this->errors = [];
        $this->nrows = 1000;
        $this->query = "";
        $this->data = [];

        return $this;
    }
}

// Example Usage:

$queryBuilder = new QueryBuilder();
$query = $queryBuilder
    ->table('table1')
    ->select(['column1', 'column2'])
    ->join('table2', 'table1.id', 'table2.table1_id')
    ->where('column1', 'value')
    ->getQuery();

$data = $queryBuilder->getData();
$errors = $queryBuilder->getErrors();
$queryBuilder->reset(); // Reset the builder for the next query
