<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Connect to database
require_once('includes/db_connect.php');

// Handle password change
$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required";
    } else if ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } else if (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long";
    } else {
        // Get user's current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $message = "Password updated successfully";
            } else {
                $error = "Error updating password";
            }
            $stmt->close();
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - FitQuest</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/dark-theme.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="profile-container">
            <div class="profile-header">
                <h2>Change Password</h2>
            </div>

            <?php if ($message): ?>
                <div class="message success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="change-password-form">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password"
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                           title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                           required>
                    <div id="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="submit-btn">Update Password</button>
            </form>
        </div>
    </main>

    <script src="js/profile.js"></script>
    <script src="js/header.js"></script>
</body>
</html>