<?php
// Set error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Set the response header to JSON format
header('Content-Type: application/json');

// Check if email and password are provided in the POST request
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

// --- SECURE: Use a prepared statement to prevent SQL Injection ---
// Select the user's id, name, email, and hashed password from the database
$sql = "SELECT id, name, email, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Database prepare error: " . $conn->error]);
    $conn->close();
    exit();
}

// Bind the email parameter and execute the query
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $hashedPassword = $user['password'];

    // Verify the provided password against the hashed password from the database
    if (password_verify($password, $hashedPassword)) {
        // --- MODIFIED: Return success with the user's ID and email ---
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $user['id'], // CRUCIAL: Now sending the user's ID
                "email" => $user['email'],
                "name" => $user['name']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>