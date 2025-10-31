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

$stats = [];
$success = true;
$message = "Stats fetched successfully";

// 1. Total Users
$result = $conn->query("SELECT COUNT(*) AS count FROM users");
if ($result) { $stats['total_users'] = $result->fetch_assoc()['count']; } else { $success = false; $message .= " Error fetching total users: " . $conn->error; }

// 2. Total Words Spelled (App-wide)
$result = $conn->query("SELECT COUNT(*) AS count FROM user_spelled_words");
if ($result) { $stats['total_words_spelled'] = $result->fetch_assoc()['count']; } else { $success = false; $message .= " Error fetching total spelled words: " . $conn->error; }

// 3. Total Fingerspelled Letters/Signs Available
$result = $conn->query("SELECT COUNT(*) AS count FROM fingerspelling_letters");
if ($result) { $stats['total_letters_available'] = $result->fetch_assoc()['count']; } else { $success = false; $message .= " Error fetching total letters: " . $conn->error; }

// 4. Total Categorized Words
$result = $conn->query("SELECT COUNT(*) AS count FROM fingerspelling_words");
if ($result) { $stats['total_categorized_words'] = $result->fetch_assoc()['count']; } else { $success = false; $message .= " Error fetching categorized words: " . $conn->error; }

echo json_encode(["success" => $success, "stats" => $stats, "message" => $message]);

$conn->close();
?>