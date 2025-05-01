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
error_log('get_fitness_data_by_date.php called with GET data: ' . print_r($_GET, true));

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get date from GET
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

error_log("Looking for data for user $user_id on date $date");

try {
    // Query to get fitness data for the specified date
    $query = $conn->prepare("
        SELECT steps, calories, active_minutes 
        FROM fitness_data 
        WHERE user_id = ? AND date = ?
    ");
    $query->bind_param("is", $user_id, $date);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        error_log("Data found: " . print_r($data, true));
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } else {
        error_log("No data found for this date");
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No data found for this date'
        ]);
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Exception: ' . $e->getMessage()
    ]);
}
?>
