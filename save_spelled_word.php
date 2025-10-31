<?php
header('Content-Type: application/json');

include 'db_connect.php';

// Get data from POST request
$user_email = $_POST['email'] ?? '';
$word = $_POST['word'] ?? '';

if (empty($user_email) || empty($word)) {
    echo json_encode(["success" => false, "message" => "Email and word are required"]);
    exit();
}

// First, get the user_id from the users table using their email
$stmt_user = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt_user->bind_param("s", $user_email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    $stmt_user->close();
    $conn->close();
    exit();
}

$user_data = $result_user->fetch_assoc();
$user_id = $user_data['id'];
$stmt_user->close();

// Now, insert the spelled word into the new table
$stmt_insert = $conn->prepare("INSERT INTO user_spelled_words (user_id, word) VALUES (?, ?)");
$stmt_insert->bind_param("is", $user_id, $word);

if ($stmt_insert->execute()) {
    echo json_encode(["success" => true, "message" => "Word saved successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save word: " . $stmt_insert->error]);
}

$stmt_insert->close();
$conn->close();
?>