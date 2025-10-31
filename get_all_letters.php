<?php
header('Content-Type: application/json');
include 'db_connect.php'; // Assume this file exists and connects to DB

$sql = "SELECT letter, video_url FROM fingerspelling_letters ORDER BY letter ASC";
$result = $conn->query($sql);

$letters = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $letters[] = $row;
    }
    echo json_encode(["success" => true, "letters" => $letters]);
} else {
    echo json_encode(["success" => false, "message" => "No letters found in database"]);
}

$conn->close();
?>