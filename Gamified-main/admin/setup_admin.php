<?php
// Include database connection
require_once('../includes/db_connect.php');

// First, check if table exists, if not create it
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    points INT DEFAULT 0,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created/verified successfully<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create admin user if it doesn't exist
$admin_username = "admin";
$admin_email = "admin@fitquest.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT); // Change this password

// Check if admin user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create admin user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $admin_username, $admin_email, $admin_password);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Please change these credentials after first login!";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
} else {
    echo "Admin user already exists";
}

$stmt->close();
$conn->close();
?>