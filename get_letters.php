<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Get the word from query or post
$word = strtoupper($_GET['word'] ?? $_POST['word'] ?? '');

if (!$word || !ctype_alpha($word)) {
    echo json_encode(["success" => false, "message" => "Invalid or missing word"]);
    exit;
}

// Split into individual letters
$letters = str_split($word);
$placeholders = implode(',', array_fill(0, count($letters), '?'));

// Prepare SQL query
$stmt = $conn->prepare("SELECT letter, video_url FROM fingerspelling_letters WHERE letter IN ($placeholders)");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL prepare error"]);
    exit;
}

// Bind letters
$stmt->bind_param(str_repeat('s', count($letters)), ...$letters);
$stmt->execute();
$result = $stmt->get_result();

$videoUrls = [];
$foundLetters = [];
$letterToUrlMap = [];
while ($row = $result->fetch_assoc()) {
    $letterToUrlMap[$row['letter']] = $row['video_url'];
}

// Maintain the order of the input word
foreach ($letters as $letter) {
    if (isset($letterToUrlMap[$letter])) {
        $videoUrls[] = $letterToUrlMap[$letter];
        $foundLetters[] = $letter;
    }
}

echo json_encode([
    "success" => true,
    "videos" => $videoUrls, // Return an ordered array of video URLs
    "letters" => $foundLetters // Return the ordered array of letters
]);

$stmt->close();
$conn->close();
?>