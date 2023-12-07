<?php

trait Database {
    private function connect(): PDO {
        $string = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER, DBPASS);
        return $con;
    }

    public function query(string $query, array $data = []): ?array {
        $con = $this->connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);

            if (is_array($result) && count($result)) {
                return $result;
            }
        }
        return [];
    }

    public function get_row(string $query, array $data = []): ?object {
        $con = $this->connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);

            if (is_array($result) && count($result)) {
                return $result[0];
            }
        }
        return null;
    }
}



// use Exception;
// use PDO;
// use PDOException;
// use PDOStatement;

trait DatabaseExtended
{
    protected string $query = "";
    protected array $data = [];
    protected bool $display = false;
    private ?PDO $db = null;

    /**
     * Fetch all rows from the query.
     */
    public function fetchAll(): array
    {
        return $this->execute()->fetchAll();
    }

    /**
     * Execute the query.
     * @return PDOStatement
     */
    public function execute(): PDOStatement
    {
        try {
            if ($this->display) {
                show($this->query);
                show($this->data);
                $this->display = false;
            }
            $statement = $this->prepare($this->query);
            $statement->execute($this->data);
            $this->query = "";
            $this->data = [];
            return $statement;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }


    protected function prepare(string $query): PDOStatement
    {
        try {
            if ($this->db == null) {
                $this->connect();
            }
            return $this->db->prepare($query);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Connect to the database.
     */
    private function connect(): void
    {
        try {
            // $connection_string = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME;
            $connection_string = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
            $this->db = new PDO($connection_string, DBUSER, DBPASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Fetch a single row from the query.
     */
    public function fetch(): object|false
    {
        return $this->execute()->fetch();
    }

    /**
     * Get database query (for debugging)
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    public function displayQuery(): object|false
    {
        $this->display = true;
        return $this;
    }

}