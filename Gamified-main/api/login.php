<?php
// Include session configuration
require_once '../includes/session_config.php';

// Include database connection
require_once '../includes/db_connect.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $response['success'] = false;
        $response['message'] = "Please enter both username and password.";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, email, password, points, CAST(is_admin AS UNSIGNED) as is_admin FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_regenerate_id();
                
                // Store data in session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['points'] = $user['points'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'] === true; // Cast to boolean
                
                // Debug log
                error_log("Login successful - User: " . $username . ", Is Admin: " . var_export($_SESSION['is_admin'], true));
                
                $response['success'] = true;
                $response['message'] = "Login successful!";
                
                // Include admin status in response
                $response['is_admin'] = (bool)$user['is_admin'] === true;
                error_log("Response admin status: " . var_export($response['is_admin'], true));
                
                // Set redirect URL based on admin status
                if ($user['is_admin']) {
                    $response['redirect'] = 'admin/dashboard.php';
                } else {
                    $response['redirect'] = 'index.php';
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Invalid password.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No account found with that username.";
        }
        
        $stmt->close();
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>