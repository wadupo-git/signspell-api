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

// Fetch all users with their total spelled words
$sql = "
    SELECT 
        u.id, 
        u.name, 
        u.email, 
        u.profile_picture, 
        u.created_at,
        COUNT(usw.id) AS total_words_spelled 
    FROM users u
    LEFT JOIN user_spelled_words usw ON u.id = usw.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(["success" => true, "users" => $users]);
} else {
    echo json_encode(["success" => true, "users" => [], "message" => "No users found"]); // Return success even if no users
}

$conn->close();
?>