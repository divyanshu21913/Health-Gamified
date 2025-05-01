<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$database = "health_gamification";

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper character handling
    if (!$conn->set_charset("utf8mb4")) {
        error_log("Error setting charset: " . $conn->error);
    }

    // Check if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    if ($result->num_rows === 0) {
        error_log("Database '$database' does not exist");
        throw new Exception("Database '$database' does not exist");
    }
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    throw $e;
}

// Note: Not closing connection here - it will be used throughout the request
?>