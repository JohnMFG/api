<?php
class DbDatabase implements IDatabase
{
    private $conn;

    public function __construct()
    {
        include 'DbConnect.php';
        $dbConnect = new DbConnect();
        $this->conn = $dbConnect->connect();
    }

    public function query($sql, $params, $fetchMode)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll($fetchMode);
    }

    public function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}