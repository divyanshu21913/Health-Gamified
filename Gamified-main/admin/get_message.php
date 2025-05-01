<?php
// Start session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Connect to database
require_once('../includes/db_connect.php');

// Set headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Initialize response
$response = ['success' => false];

// Check if ID is provided
if (isset($_GET['id'])) {
    $message_id = (int)$_GET['id'];
    
    // Get message details
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $response['success'] = true;
        $response['message'] = $result->fetch_assoc();
    } else {
        $response['message'] = 'Message not found';
    }
    
    $stmt->close();
} else {
    $response['message'] = 'No message ID provided';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>