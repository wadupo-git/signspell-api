<?php
header('Content-Type: application/json');

include 'db_connect.php';

// Get ID from the POST request
$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(["success" => false, "message" => "ID is required"]);
    exit();
}

// Prepare and execute the delete query
$stmt = $conn->prepare("DELETE FROM fingerspelling_words WHERE id = ?");
$stmt->bind_param("i", $id); // 'i' for integer

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Word deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Word not found or already deleted"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete word: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>