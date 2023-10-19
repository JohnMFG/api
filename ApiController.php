<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");


$environment = 'testing';

if ($environment === 'testing') {
    include 'MockPDO.php'; // Include your mock database setup here
    $conn = new MockPDO(); 
} else {
    include 'DbConnect.php';
    $objDb = new DbConnect();
    $conn = $objDb->connect();
}

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        $sql = "SELECT * FROM users";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($users);
        break;
    case "POST":
        $user = json_decode(file_get_contents('php://input'));


        if (empty($user->name) || empty($user->email) || empty($user->mobile)) {
            $response = ['status' => 0, 'message' => 'Please provide all required fields.'];
        } else {
            $sql = "INSERT INTO users(id, name, email, status, mobile, created_at) VALUES(null, :name, :email, :status, :mobile, :created_at)";
            $stmt = $conn->prepare($sql);
            $created_at = date('Y-m-d');
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':status', $user->status);
            $stmt->bindParam(':mobile', $user->mobile);
            $stmt->bindParam(':created_at', $created_at);

            if ($stmt->execute()) {
                $insertedId = $conn->lastInsertId();
                $response = ['status' => 1, 'message' => 'Record created successfully.',
                            'created_id' => $insertedId];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to create record.'];
            }
        }
        echo json_encode($response);
        break;

    case "PUT":
        $user = json_decode(file_get_contents('php://input'));


        if (empty($user->id) || empty($user->name) || empty($user->email) || empty($user->mobile)) {
            $response = ['status' => 0, 'message' => 'Please provide all required fields.'];
        } else {
            $sql = "UPDATE users SET name= :name, email =:email, status =:status, mobile =:mobile, updated_at =:updated_at WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $updated_at = date('Y-m-d');
            $stmt->bindParam(':id', $user->id);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':status', $user->status);
            $stmt->bindParam(':mobile', $user->mobile);
            $stmt->bindParam(':updated_at', $updated_at);

            if ($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record updated successfully.'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to update record.'];
            }
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM users WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);

        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($response);
        break;
}










// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: *");
// header("Access-Control-Allow-Methods: *");


// $environment = 'testin';


// if ($environment === 'testing') {
//     include 'MockPDO.php'; // Include your mock database setup here
//     $conn = new MockPDO(); 
// } else {
//     include 'DbConnect.php';
//     $objDb = new DbConnect();
//     $conn = $objDb->connect();
// }

// $database = new Database($conn);

// $method = $_SERVER['REQUEST_METHOD'];
// switch ($method) {
//     case "GET":
//         $sql = "SELECT * FROM users";
//         $path = explode('/', $_SERVER['REQUEST_URI']);
//         if (isset($path[3]) && is_numeric($path[3])) {
//             $sql .= " WHERE id = :id";
//             $stmt = $database->executeQuery($sql, [':id' => $path[3]]);
//             $users = $stmt->fetch(PDO::FETCH_ASSOC);
//         } else {
//             $stmt = $database->executeQuery($sql);
//             $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         }

//         echo json_encode($users);
//         break;

//     case "POST":
//         $user = json_decode(file_get_contents('php://input'));
//         $response = [];

//         if (empty($user->name) || empty($user->email) || empty($user->mobile)) {
//             $response = ['status' => 0, 'message' => 'Please provide all required fields.'];
//         } else {
//             $sql = "INSERT INTO users(id, name, email, status, mobile, created_at) VALUES(null, :name, :email, :status, :mobile, :created_at)";
//             $stmt = $database->executeQuery($sql, [
//                 ':name' => $user->name,
//                 ':email' => $user->email,
//                 ':status' => $user->status,
//                 ':mobile' => $user->mobile,
//                 ':created_at' => date('Y-m-d'),
//             ]);

//             if ($stmt->execute()) {
//                 $insertedId = $conn->lastInsertId();
//                 $response = ['status' => 1, 'message' => 'Record created successfully.', 'created_id' => $insertedId];
//             } else {
//                 $response = ['status' => 0, 'message' => 'Failed to create record.'];
//             }
//         }
//         echo json_encode($response);
//         break;

//     case "PUT":
//         $user = json_decode(file_get_contents('php://input'));
//         $response = [];

//         if (empty($user->id) || empty($user->name) || empty($user->email) || empty($user->mobile)) {
//             $response = ['status' => 0, 'message' => 'Please provide all required fields.'];
//         } else {
//             $sql = "UPDATE users SET name= :name, email =:email, status =:status, mobile =:mobile, updated_at =:updated_at WHERE id = :id";
//             $stmt = $database->executeQuery($sql, [
//                 ':id' => $user->id,
//                 ':name' => $user->name,
//                 ':email' => $user->email,
//                 ':status' => $user->status,
//                 ':mobile' => $user->mobile,
//                 ':updated_at' => date('Y-m-d'),
//             ]);

//             if ($stmt->execute()) {
//                 $response = ['status' => 1, 'message' => 'Record updated successfully.'];
//             } else {
//                 $response = ['status' => 0, 'message' => 'Failed to update record.'];
//             }
//         }
//         echo json_encode($response);
//         break;

//     // case "DELETE":
//     //     $path = explode('/', $_SERVER['REQUEST_URI']);
//     //     $response = [];

//     //     if (isset($path[3]) && is_numeric($path[3])) {
//     //         $sql = "DELETE FROM users WHERE id = :id";
//     //         $stmt = $database->executeQuery($sql, [':id' => $path[3]);

//     //         if ($stmt->execute()) {
//     //             $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
//     //         } else {
//     //             $response = ['status' => 0, 'message' => 'Failed to delete record.'];
//     //         }
//     //     } else {
//     //         $response = ['status' => 0, 'message' => 'Invalid request.'];
//     //     }

//     //     echo json_encode($response);
//     //     break;
// }