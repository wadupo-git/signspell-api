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

// Prepare and execute the query to get a random letter from the fingerspelling_letters table
// We order by RAND() to get a random row and limit to 1
$stmt = $conn->prepare("SELECT letter, video_url FROM fingerspelling_letters ORDER BY RAND() LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Return success with the random letter and its video URL
    echo json_encode(["success" => true, "letter" => $row['letter'], "video_url" => $row['video_url']]);
} else {
    // Return a failure message if no letters are found in the table
    echo json_encode(["success" => false, "message" => "No letters found in the database"]);
}

$stmt->close();
$conn->close();
?>