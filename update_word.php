<?php
header('Content-Type: application/json');

// Database connection details
$$servername = "localhost";
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
$id = $_POST['id'] ?? '';
$word = $_POST['word'] ?? '';
$category = $_POST['category'] ?? '';
$videoUrl = $_POST['video_url'] ?? NULL; // Can be NULL

if (empty($id) || empty($word) || empty($category)) {
    echo json_encode(["success" => false, "message" => "ID, word, and category are required"]);
    exit();
}

// Prepare and execute the update query
$stmt = $conn->prepare("UPDATE fingerspelling_words SET word = ?, category = ?, video_url = ? WHERE id = ?");
$stmt->bind_param("sssi", $word, $category, $videoUrl, $id); // 'sssi' for string, string, string (for URL), integer

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Word updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Word not found or no changes made"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Failed to update word: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>