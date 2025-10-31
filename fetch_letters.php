<?php
header('Content-Type: application/json');

include 'db_connect.php';

// Fetch all letters from fingerspelling_letters table
// Make sure to select the 'description' column
$sql = "SELECT id, letter, video_url, description FROM fingerspelling_letters ORDER BY letter ASC";
$result = $conn->query($sql);

$letters = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $letters[] = $row;
    }
    echo json_encode(["success" => true, "letters" => $letters]);
} else {
    echo json_encode(["success" => true, "letters" => [], "message" => "No letters found"]);
}

$conn->close();
?>