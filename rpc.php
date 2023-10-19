


<?php
/* POST
$url = "http://localhost/api/rpc.php?action=create"
$body = @{
    action = "create"
    name = "Johny"
    email = "john@example.com"
    mobile = "1234567890"
    status = "ACTIVE"
} | ConvertTo-Json

Invoke-WebRequest -Uri $url -Method POST -Body $body -Headers @{"Content-Type"="application/json"}
*/



/* EDIT
$url = "http://localhost/api/rpc.php?action=edit"
$body = @{
    action = "edit"
    id = 98
    name = "AAAA"
    email = "updated@example.com"
    mobile = "9876543210"
} | ConvertTo-Json

Invoke-WebRequest -Uri $url -Method PUT -Body $body -Headers @{"Content-Type"="application/json"}
*/


/* DELETE
$uri = "http://localhost/api/rpc.php"
$requestHeaders = @{
    "Content-Type" = "application/json"
}
$requestBody = @{
    "action" = "delete"
    "id" = 92
} | ConvertTo-Json

Invoke-RestMethod -Uri $uri -Method Delete -Headers $requestHeaders -Body $requestBody
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

function userExists($conn, $userId) {
    $checkSql = "SELECT id FROM users WHERE id = :id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':id', $userId);
    $checkStmt->execute();
    return $checkStmt->fetch(PDO::FETCH_ASSOC);
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
    $user = json_decode(file_get_contents('php://input'));

    if (empty($user->action)) {
        $response = ['status' => 0, 'message' => 'Please provide the action parameter.'];
        echo json_encode($response);
    } else {
        $action = $user->action;

        if ($action === 'create') {
            if ($method === 'POST') {
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
                        $response = [
                            'status' => 1,
                            'message' => 'Record created successfully.',
                            'created_id' => $insertedId
                        ];
                    } else {
                        $response = ['status' => 0, 'message' => 'Failed to create record.'];
                    }
                }
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid method for create action.']);
            }
        } elseif ($action === 'edit') {
            if ($method === 'PUT') {
                if (empty($user->id) || empty($user->name) || empty($user->email) || empty($user->mobile)) {
                    $response = ['status' => 0, 'message' => 'Please provide all required fields.'];
                } else {
                    $existingUser = userExists($conn, $user->id);
                    if (!$existingUser) {
                        $response = ['status' => 0, 'message' => 'User with the specified ID does not exist.'];
                    } else {
                        $sql = "UPDATE users SET name = :name, email = :email, status = :status, mobile = :mobile, updated_at = :updated_at WHERE id = :id";
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
                }
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid method for edit action.']);
            }
        } elseif ($action === 'delete') {
            if ($method === 'DELETE') {
                if (empty($user->id)) {
                    $response = ['status' => 0, 'message' => 'Please provide the user ID to delete.'];
                } else {
                    $existingRecord = userExists($conn, $user->id);
                    if (!$existingRecord) {
                        $response = ['status' => 0, 'message' => 'Record with the specified ID does not exist.'];
                    } else {
                        $deleteSql = "DELETE FROM users WHERE id = :id";
                        $deleteStmt = $conn->prepare($deleteSql);
                        $deleteStmt->bindParam(':id', $user->id);

                        if ($deleteStmt->execute()) {
                            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
                        } else {
                            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
                        }
                    }
                }
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid method for delete action.']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid action.']);
        }
    }
}

?>


