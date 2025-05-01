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
error_log('get_fitness_data.php called with GET data: ' . print_r($_GET, true));

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get last 30 days of data for the table, but use 7 days for averages
$end_date = date('Y-m-d');
$history_start_date = date('Y-m-d', strtotime('-30 days')); // For table display
$avg_start_date = date('Y-m-d', strtotime('-7 days')); // For averages

error_log("Date range: $history_start_date to $end_date");

try {
    // Query to get fitness data for the history table
    $query = $conn->prepare("
        SELECT date, steps, calories, active_minutes
        FROM fitness_data
        WHERE user_id = ? AND date BETWEEN ? AND ?
        ORDER BY date DESC
    ");
    $query->bind_param("iss", $user_id, $history_start_date, $end_date);
    $query->execute();
    $result = $query->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    error_log("Found " . count($data) . " records");

    // Get user's average metrics
    $avg_query = $conn->prepare("
        SELECT 
            AVG(steps) as avg_steps,
            AVG(calories) as avg_calories,
            AVG(active_minutes) as avg_active_minutes
        FROM fitness_data
        WHERE user_id = ? AND date BETWEEN ? AND ?
    ");
    $avg_query->bind_param("iss", $user_id, $avg_start_date, $end_date);
    $avg_query->execute();
    $avg_result = $avg_query->get_result()->fetch_assoc();

    // Generate suggestions based on data
    $suggestions = generateSuggestions($data, $avg_result);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $data,
        'averages' => $avg_result,
        'suggestions' => $suggestions
    ]);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Exception: ' . $e->getMessage()
    ]);
}

// Function to generate personalized suggestions
function generateSuggestions($data, $averages) {
    $suggestions = [];
    
    // Check if there's enough data
    if (count($data) < 3) {
        $suggestions[] = "Track your fitness data for at least 3 days to receive personalized suggestions.";
        return $suggestions;
    }
    
    // Steps suggestions
    $avg_steps = $averages['avg_steps'];
    if ($avg_steps < 5000) {
        $suggestions[] = "Try to increase your daily steps. Aim for at least 5,000 steps per day.";
        $suggestions[] = "Take short walking breaks during your day to increase step count.";
    } elseif ($avg_steps < 10000) {
        $suggestions[] = "You're on the right track with steps! Try to reach 10,000 steps daily for optimal health benefits.";
    } else {
        $suggestions[] = "Great job maintaining a high step count! Consider adding intensity with brisk walking or inclines.";
    }
    
    // Calories suggestions
    $avg_calories = $averages['avg_calories'];
    if ($avg_calories < 1000) {
        $suggestions[] = "Your calorie burn seems low. Try adding more physical activity to your routine.";
    } elseif ($avg_calories < 2000) {
        $suggestions[] = "You're burning a good amount of calories. Mix cardio and strength training for better results.";
    } else {
        $suggestions[] = "Excellent calorie burn! Make sure you're also getting proper nutrition to support your activity level.";
    }
    
    // Active minutes suggestions
    $avg_active = $averages['avg_active_minutes'];
    if ($avg_active < 30) {
        $suggestions[] = "Aim for at least 30 minutes of moderate activity daily for heart health.";
    } elseif ($avg_active < 60) {
        $suggestions[] = "You're meeting basic activity recommendations. Try to reach 60 minutes for additional health benefits.";
    } else {
        $suggestions[] = "You're very active! Consider adding variety to your workouts to challenge different muscle groups.";
    }
    
    // Check for trends
    $last_three_days = array_slice($data, -3);
    $trend_down = true;
    for ($i = 1; $i < count($last_three_days); $i++) {
        if ($last_three_days[$i]['steps'] >= $last_three_days[$i-1]['steps']) {
            $trend_down = false;
            break;
        }
    }
    
    if ($trend_down) {
        $suggestions[] = "Your activity has been decreasing over the last few days. Try to stay consistent with your routine.";
    }
    
    return $suggestions;
}
?>
