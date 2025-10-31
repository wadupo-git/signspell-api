<?php
include 'db_connect.php';

header('Content-Type: application/json'); // Ensure response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'] ?? ''; // Expecting 'id' as per your database schema

    if (empty($userId)) {
        echo json_encode(["success" => false, "message" => "User ID is required."]);
        exit;
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?"); // 'id' is your primary key
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL prepare error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $userId); // 'i' denotes integer type for id
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "User deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "User not found or already deleted."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>