<?php
header('Content-Type: application/json');

include 'db_connect.php';

// Get user ID from the GET request
$userId = $_GET['id'] ?? '';

if (empty($userId)) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit();
}

// 1. Fetch user details
$stmt_user = $conn->prepare("SELECT id, name, email, profile_picture, created_at FROM users WHERE id = ?");
$stmt_user->bind_param("i", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

$user_details = null;
if ($result_user->num_rows > 0) {
    $user_details = $result_user->fetch_assoc();
}
$stmt_user->close();

if (!$user_details) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    $conn->close();
    exit();
}

// 2. Fetch user's spelling history
$spelling_history = [];
$stmt_history = $conn->prepare("SELECT word, created_at FROM user_spelled_words WHERE user_id = ? ORDER BY created_at DESC");
$stmt_history->bind_param("i", $userId);
$stmt_history->execute();
$result_history = $stmt_history->get_result();

while ($row = $result_history->fetch_assoc()) {
    $spelling_history[] = $row;
}
$stmt_history->close();

echo json_encode(["success" => true, "user_details" => $user_details, "spelling_history" => $spelling_history]);

$conn->close();
?>