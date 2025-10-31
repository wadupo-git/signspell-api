<?php
// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

header('Content-Type: application/json');

// Check if all required data is provided via POST
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    $conn->close();
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
// Hash the password securely
$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// --- SECURE: Use a prepared statement to prevent SQL Injection ---
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Database prepare error: " . $conn->error]);
    $conn->close();
    exit();
}

// Bind the parameters: 'sss' for three strings
$stmt->bind_param("sss", $name, $email, $hashed_password);

// Execute the prepared statement
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful"]);
} else {
    // Handle database errors (e.g., duplicate email)
    if ($conn->errno == 1062) { // 1062 is the error code for duplicate entry
        echo json_encode(["success" => false, "message" => "Error: Email already registered."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>