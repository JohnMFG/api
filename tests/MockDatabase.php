<?php
class MockDatabase {
    public function executeQuery($sql, $parameters = null) {
        // Implement your mock behavior here.
        // You can return predefined data or simulate database operations.
        
        switch ($sql) {
            case "SELECT * FROM users":
                return [
                    ['id' => 1, 'name' => 'User 1', 'email' => 'user1@example.com'],
                    ['id' => 2, 'name' => 'User 2', 'email' => 'user2@example.com'],
                    // Add more mock data as needed.
                ];
            case "SELECT * FROM users WHERE id = :id":
                // Simulate the case when a specific user is requested.
                if ($parameters && isset($parameters[':id'])) {
                    $userId = $parameters[':id'];
                    if ($userId === 1) {
                        return ['id' => 1, 'name' => 'User 1', 'email' => 'user1@example.com'];
                    }
                }
                break;
            case "INSERT INTO users(id, name, email, status, mobile, created_at) VALUES(null, :name, :email, :status, :mobile, :created_at)":
                // Simulate the successful insertion of a new user.
                return ['status' => 1, 'message' => 'Record created successfully.', 'created_id' => 123];
            // Add more cases for other SQL queries.
        }

        // Return a default value or handle other cases as needed.
        return [];
    }
}

