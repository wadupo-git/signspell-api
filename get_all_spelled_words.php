<?php
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP user
$password = ""; // No password by default
$dbname = "signspell_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get user email from the POST request
$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit();
}

// First, get the user_id from the users table using their email
$stmt_user = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt_user->bind_param("s", $email);
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

// Now, get all spelled words for this user
$stmt_words = $conn->prepare("SELECT word FROM user_spelled_words WHERE user_id = ? ORDER BY created_at DESC");
$stmt_words->bind_param("i", $user_id);
$stmt_words->execute();
$result_words = $stmt_words->get_result();

$words_array = [];
while ($row = $result_words->fetch_assoc()) {
    $words_array[] = $row['word'];
}

echo json_encode(["success" => true, "words" => $words_array]);

$stmt_words->close();
$conn->close();
?>