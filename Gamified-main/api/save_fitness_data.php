<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Include database connection
require_once('../includes/db_connect.php');

// Log the request for debugging
error_log('save_fitness_data.php called with POST data: ' . print_r($_POST, true));

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Get data from POST
    $steps = isset($_POST['steps']) ? (int)$_POST['steps'] : 0;
    $calories = isset($_POST['calories']) ? (int)$_POST['calories'] : 0;
    $active_minutes = isset($_POST['active_minutes']) ? (int)$_POST['active_minutes'] : 0;
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
    
    // Validate data
    if ($steps < 0 || $calories < 0 || $active_minutes < 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit();
    }
    
    // Log the processed data
    error_log("Processed data: user_id=$user_id, date=$date, steps=$steps, calories=$calories, active_minutes=$active_minutes");
    
    try {
        // Check if entry for this date already exists
        $check_query = $conn->prepare("SELECT id FROM fitness_data WHERE user_id = ? AND date = ?");
        $check_query->bind_param("is", $user_id, $date);
        $check_query->execute();
        $result = $check_query->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing entry
            $row = $result->fetch_assoc();
            $stmt = $conn->prepare("UPDATE fitness_data SET steps = ?, calories = ?, active_minutes = ? WHERE id = ?");
            $stmt->bind_param("iiii", $steps, $calories, $active_minutes, $row['id']);
            error_log("Updating existing entry with ID: " . $row['id']);
        } else {
            // Insert new entry
            $stmt = $conn->prepare("INSERT INTO fitness_data (user_id, date, steps, calories, active_minutes) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isiii", $user_id, $date, $steps, $calories, $active_minutes);
            error_log("Inserting new entry");
        }
        
        $result = $stmt->execute();
        error_log("Query execution result: " . ($result ? 'success' : 'failure'));
        
        if ($result) {
            // Calculate points based on activity
            $points_earned = calculatePoints($steps, $calories, $active_minutes);
            
            // Update user points
            if ($points_earned > 0) {
                $update_points = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                $update_points->bind_param("ii", $points_earned, $user_id);
                $update_points->execute();
                
                // Update session points
                $_SESSION['points'] += $points_earned;
                error_log("Points earned: $points_earned");
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Fitness data saved successfully',
                'points_earned' => $points_earned
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Error saving data: ' . $stmt->error
            ]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Exception: ' . $e->getMessage()
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Function to calculate points based on activity
function calculatePoints($steps, $calories, $active_minutes) {
    $points = 0;
    
    // Points for steps (1 point per 1000 steps)
    $points += floor($steps / 1000);
    
    // Points for calories (1 point per 200 calories)
    $points += floor($calories / 200);
    
    // Points for active minutes (1 point per 10 minutes)
    $points += floor($active_minutes / 10);
    
    return $points;
}
?>
