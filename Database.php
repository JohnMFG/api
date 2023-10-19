<?php
class Database {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function executeQuery($sql, $parameters = null) {
        $stmt = $this->conn->prepare($sql);
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $stmt->bindParam($key, $value);
            }
        }
        $stmt->execute();
        return $stmt;
    }
}