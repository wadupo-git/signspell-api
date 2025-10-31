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

// Fetch all words from fingerspelling_words table
$sql = "SELECT id, word, category, video_url, created_at FROM fingerspelling_words ORDER BY word ASC";
$result = $conn->query($sql);

$words = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $words[] = $row;
    }
    echo json_encode(["success" => true, "words" => $words]);
} else {
    echo json_encode(["success" => true, "words" => [], "message" => "No words found"]);
}

$conn->close();
?>