<?php
header('Content-Type: application/json');

include 'db_connect.php';

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