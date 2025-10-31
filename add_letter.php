<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $letter = strtoupper($_POST['letter'] ?? ''); // Convert to uppercase for consistency
    $videoUrl = $_POST['video_url'] ?? '';

    if (empty($letter) || empty($videoUrl)) {
        echo json_encode(["success" => false, "message" => "Letter and Video URL are required."]);
        exit;
    }

    // Check if the letter already exists
    $stmt_check = $conn->prepare("SELECT id FROM fingerspelling_letters WHERE letter = ?");
    if (!$stmt_check) {
        echo json_encode(["success" => false, "message" => "SQL prepare error (check): " . $conn->error]);
        exit;
    }
    $stmt_check->bind_param("s", $letter);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Letter '$letter' already exists."]);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Insert the new letter and video URL
    $stmt = $conn->prepare("INSERT INTO fingerspelling_letters (letter, video_url) VALUES (?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL prepare error (insert): " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ss", $letter, $videoUrl); // 's' for string, 's' for string

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Letter '$letter' added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding letter: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>