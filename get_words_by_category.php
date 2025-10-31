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

// Get the category from the GET request
$category = $_GET['category'] ?? '';

if (empty($category)) {
    echo json_encode(["success" => false, "message" => "Category is required"]);
    exit();
}

// Prepare and execute the query to get words by category
$stmt = $conn->prepare("SELECT word, video_url FROM fingerspelling_words WHERE category = ? ORDER BY word ASC");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$words = [];
while ($row = $result->fetch_assoc()) {
    $words[] = [
        'word' => $row['word'],
        'video_url' => $row['video_url']
    ];
}

if (!empty($words)) {
    echo json_encode(["success" => true, "words" => $words]);
} else {
    echo json_encode(["success" => false, "message" => "No words found for this category"]);
}

$stmt->close();
$conn->close();
?>