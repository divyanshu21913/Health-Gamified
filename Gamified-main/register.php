<?php
// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitQuest</title>
    <link rel="stylesheet" href="css/register.css">
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
                <h2>Create an Account</h2>
            </div>
            
            <div class="auth-form">
                <div id="register-message" class="auth-message"></div>
                
                <form id="register-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" required>
                    </div>
                    
                    <button  type="submit" class="auth-btn" >Register</button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const messageDiv = document.getElementById('register-message');
            
            // Validate passwords match
            if (password !== confirmPassword) {
                messageDiv.className = 'auth-message error';
                messageDiv.textContent = 'Passwords do not match';
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password);
            
            // Send registration request
            fetch('api/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.className = 'auth-message success';
                    messageDiv.textContent = data.message;
                    
                    // Redirect to login page after successful registration
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
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