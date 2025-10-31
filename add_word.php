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

// Get data from the POST request
$word = $_POST['word'] ?? '';
$category = $_POST['category'] ?? '';
$videoUrl = $_POST['video_url'] ?? NULL; // Can be NULL

if (empty($word) || empty($category)) {
    echo json_encode(["success" => false, "message" => "Word and category are required"]);
    exit();
}

// Check if word already exists in the same category (optional, but good for data integrity)
$stmt_check = $conn->prepare("SELECT id FROM fingerspelling_words WHERE word = ? AND category = ?");
$stmt_check->bind_param("ss", $word, $category);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Word '{$word}' already exists in '{$category}' category"]);
    $stmt_check->close();
    $conn->close();
    exit();
}
$stmt_check->close();

// Prepare and execute the insert query
$stmt = $conn->prepare("INSERT INTO fingerspelling_words (word, category, video_url) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $word, $category, $videoUrl);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Word added successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add word: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>