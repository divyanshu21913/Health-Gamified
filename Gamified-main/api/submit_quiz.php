<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';

// Initialize response array
$response = array();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User not logged in";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Get answers from POST data
    $answers = json_decode($_POST['answers'], true);
    
    if (!$answers) {
        $response['success'] = false;
        $response['message'] = "Invalid answers format";
    } else {
        // Calculate score
        $score = 0;
        $total_questions = count($answers);
        
        foreach ($answers as $question_id => $user_answer) {
            // Get correct answer from database
            $query = "SELECT correct_answer FROM questions WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if ($user_answer === $row['correct_answer']) {
                    $score++;
                }
            }
            
            $stmt->close();
        }
        
        // Save quiz result
        $insert_query = "INSERT INTO quiz_results (user_id, score, total_questions) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iii", $user_id, $score, $total_questions);
        
        if ($insert_stmt->execute()) {
            // Update user points (10 points per correct answer)
            $points_earned = $score * 10;
            $update_query = "UPDATE users SET points = points + ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ii", $points_earned, $user_id);
            $update_stmt->execute();
            
            // Update session points
            $_SESSION['points'] += $points_earned;
            
            $response['success'] = true;
            $response['message'] = "Quiz submitted successfully";
            $response['score'] = $score;
            $response['total'] = $total_questions;
            $response['points_earned'] = $points_earned;
            
            $update_stmt->close();
        } else {
            $response['success'] = false;
            $response['message'] = "Error saving quiz result: " . $insert_stmt->error;
        }
        
        $insert_stmt->close();
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