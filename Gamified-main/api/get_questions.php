<?php
// Include database connection
require_once '../includes/db_connect.php';

// Initialize response array
$response = array();

// Get all questions
$query = "SELECT id, question, option_a, option_b, option_c, option_d FROM questions";
$result = $conn->query($query);

if ($result) {
    $questions = array();
    
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    
    $response['success'] = true;
    $response['questions'] = $questions;
} else {
    $response['success'] = false;
    $response['message'] = "Error retrieving questions: " . $conn->error;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>