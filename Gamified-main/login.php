<?php
// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitQuest</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <span class="material-symbols-outlined">fitness_center</span>
                    <h1>FitQuest</h1>
                </div>
                <h2>Login to Your Account</h2>
            </div>
            
            <div class="auth-form">
                <div id="login-message" class="auth-message"></div>
                
                <form id="login-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="auth-btn">Login</button>
                </form>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const messageDiv = document.getElementById('login-message');
    
    // Create form data
    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    
    // Send login request
    fetch('api/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Login response:', data); // Debug log
        if (data.success) {
            messageDiv.className = 'auth-message success';
            messageDiv.textContent = data.message;
            
            // Redirect based on admin status
            setTimeout(() => {
                if (data.is_admin) {
                    console.log('Admin login detected, redirecting to dashboard...'); // Debug log
                    window.location.href = 'admin/dashboard.php';
                } else {
                    console.log('Regular user login, redirecting to index...'); // Debug log
                    window.location.href = 'index.php';
                }
            }, 1000);
        } else {
            messageDiv.className = 'auth-message error';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        messageDiv.className = 'auth-message error';
        messageDiv.textContent = 'An error occurred. Please try again.';
        console.error('Error:', error);
    });
});
    </script>
</body>
</html>