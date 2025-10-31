<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "Letter ID is required."]);
        exit;
    }

    // Delete the letter entry
    $stmt = $conn->prepare("DELETE FROM fingerspelling_letters WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL prepare error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $id); // 'i' denotes integer type for id

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Letter ID $id deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Letter ID $id not found or already deleted."]);
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