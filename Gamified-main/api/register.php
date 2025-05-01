<?php
// Include database connection
require_once '../includes/db_connect.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        $response['success'] = false;
        $response['message'] = "All fields are required";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['success'] = false;
            $response['message'] = "Username or email already exists";
        } else {
            // Insert new user
            $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($insert_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Registration successful";
            } else {
                $response['success'] = false;
                $response['message'] = "Error: " . $insert_stmt->error;
            }
            
            $insert_stmt->close();
        }
        
        $check_stmt->close();
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>