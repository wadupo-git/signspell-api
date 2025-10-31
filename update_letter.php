<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $letter = strtoupper($_POST['letter'] ?? ''); // Convert to uppercase for consistency
    $videoUrl = $_POST['video_url'] ?? '';

    if (empty($id) || empty($letter) || empty($videoUrl)) {
        echo json_encode(["success" => false, "message" => "ID, Letter, and Video URL are required."]);
        exit;
    }

    // Check if the letter already exists for a *different* ID (to prevent duplicate letters)
    $stmt_check = $conn->prepare("SELECT id FROM fingerspelling_letters WHERE letter = ? AND id != ?");
    if (!$stmt_check) {
        echo json_encode(["success" => false, "message" => "SQL prepare error (check for duplicate letter): " . $conn->error]);
        exit;
    }
    $stmt_check->bind_param("si", $letter, $id); // 's' for string (letter), 'i' for integer (id)
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Letter '$letter' already exists for another entry. Please choose a unique letter."]);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Update the letter and video URL
    $stmt = $conn->prepare("UPDATE fingerspelling_letters SET letter = ?, video_url = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL prepare error (update): " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssi", $letter, $videoUrl, $id); // 's' for letter, 's' for video_url, 'i' for id

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Letter ID $id updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No changes made or letter ID $id not found."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error updating letter: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>