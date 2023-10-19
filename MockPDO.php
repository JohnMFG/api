<?php

class MockPDO extends PDO {
    private $data = [];

    public function __construct() {
        // The constructor for MockPDO
        parent::__construct('sqlite::memory:', 'username', 'password');
    }

    public function seedData($sql, $data) {
        // Store data in the mock database with the SQL query as the key
        $this->data[$sql] = $data;
    }
}
