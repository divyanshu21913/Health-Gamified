<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Since this file is included from the root directory, use path relative to root
    require_once('includes/db_connect.php');

    // SQL to create the contact_messages table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        error_log("Contact messages table verified/created successfully");
    } else {
        error_log("Error creating/verifying table: " . $conn->error);
        throw new Exception("Error creating table: " . $conn->error);
    }

} catch (Exception $e) {
    error_log("Setup contact messages error: " . $e->getMessage());
    throw $e;
} finally {
    // Don't close the connection here since it's needed by the calling script
    // The connection will be closed by the main script
}
?>