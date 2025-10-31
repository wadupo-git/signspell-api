<?php
header('Content-Type: application/json');

include 'db_connect.php';

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