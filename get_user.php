<?php
header('Content-Type: application/json');

include 'db_connect.php';

// Get user email from the POST request
$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit();
}

// Prepare and execute the query to get user data
$stmt = $conn->prepare("SELECT id, name, email, profile_picture, created_at FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // --- NEW: Get Total Words Spelled by the user ---
    $total_words_stmt = $conn->prepare("SELECT COUNT(*) AS total_count FROM user_spelled_words WHERE user_id = ?");
    $total_words_stmt->bind_param("i", $user_id);
    $total_words_stmt->execute();
    $total_words_result = $total_words_stmt->get_result();
    $total_words_data = $total_words_result->fetch_assoc();
    $total_words_spelled = $total_words_data['total_count'];
    $total_words_stmt->close();

    // --- NEW: Get Recent Words Spelled by the user (e.g., last 5) ---
    $recent_words_stmt = $conn->prepare("SELECT word FROM user_spelled_words WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $recent_words_stmt->bind_param("i", $user_id);
    $recent_words_stmt->execute();
    $recent_words_result = $recent_words_stmt->get_result();
    $recent_words = [];
    while ($row = $recent_words_result->fetch_assoc()) {
        $recent_words[] = $row['word'];
    }
    $recent_words_stmt->close();

    // Add new data to the user array
    $user['total_words_spelled'] = $total_words_spelled;
    $user['recent_words'] = $recent_words;

    // Return success with user data and stats
    echo json_encode(["success" => true, "user" => $user]);

} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>