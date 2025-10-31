<?php
include 'db_connect.php';

header('Content-Type: application/json'); // Ensure response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $profilePictureUrl = null; // Initialize to null, will be updated if a file is uploaded

    // Define the upload directory. IMPORTANT: This path must be writable by your web server.
    // This path is relative to the location of this PHP script.
    // Make sure 'uploads/profile_pictures/' directory exists and is writable.
    // Example: if update_user.php is in /var/www/html/api/, then the directory should be /var/www/html/api/uploads/profile_pictures/
    $uploadDir = 'uploads/profile_pictures/';
    if (!is_dir($uploadDir)) {
        // Attempt to create the directory if it doesn't exist
        mkdir($uploadDir, 0755, true); // 0755 permissions, recursive true
    }

    // Basic validation for required fields
    if (empty($userId) || empty($name) || empty($email)) {
        echo json_encode(["success" => false, "message" => "User ID, name, and email are required."]);
        exit;
    }

    // --- Profile Picture Upload Handling ---
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize filename and generate a unique name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        // Allowed file extensions
        $allowedFileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
        
        if (in_array($fileExtension, $allowedFileExtensions)) {
            // Check file size (e.g., max 5MB)
            if ($fileSize < 5000000) { // 5MB limit
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // File successfully moved, now store its relative path in the database
                    $profilePictureUrl = $uploadDir . $newFileName;
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to move uploaded file."]);
                    exit;
                }
            } else {
                echo json_encode(["success" => false, "message" => "File size exceeds limit (5MB)."]);
                exit;
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed."]);
            exit;
        }
    }
    // --- End Profile Picture Upload Handling ---

    // Prepare the SQL query
    $updateFields = [];
    $bindTypes = "";
    $bindParams = [];

    // Always update name and email
    $updateFields[] = "name = ?";
    $bindTypes .= "s";
    $bindParams[] = &$name; // Pass by reference

    $updateFields[] = "email = ?";
    $bindTypes .= "s";
    $bindParams[] = &$email; // Pass by reference

    // Only add profile_picture to update if a new one was uploaded
    if ($profilePictureUrl !== null) {
        $updateFields[] = "profile_picture = ?";
        $bindTypes .= "s";
        $bindParams[] = &$profilePictureUrl; // Pass by reference
    }

    $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $bindTypes .= "i";
    $bindParams[] = &$userId; // Pass by reference

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL prepare error: " . $conn->error]);
        exit;
    }

    // Bind parameters dynamically
    call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindParams));

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "User updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No changes made or user not found."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>