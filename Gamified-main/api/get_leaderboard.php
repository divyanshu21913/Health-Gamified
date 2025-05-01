<?php
// Include database connection
require_once '../includes/db_connect.php';

// Initialize response array
$response = array();

// Get top 10 users by points
$query = "SELECT id, username, points FROM users ORDER BY points DESC LIMIT 10";
$result = $conn->query($query);

if ($result) {
    $leaderboard = array();
    $rank = 1;
    
    while ($row = $result->fetch_assoc()) {
        $row['rank'] = $rank++;
        $leaderboard[] = $row;
    }
    
    $response['success'] = true;
    $response['leaderboard'] = $leaderboard;
} else {
    $response['success'] = false;
    $response['message'] = "Error retrieving leaderboard: " . $conn->error;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>