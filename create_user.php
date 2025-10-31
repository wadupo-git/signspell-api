<?php
header('Content-Type: application/json');

include 'db_connect.php';

// Get data from the POST request
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Name, email, and password are required"]);
    exit();
}

// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if email already exists
$stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered"]);
    $stmt_check->close();
    $conn->close();
    exit();
}
$stmt_check->close();


// Prepare and execute the insert query
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create user: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>