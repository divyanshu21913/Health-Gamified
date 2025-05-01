<?php
require_once '../includes/session_config.php';

// Debug session status before any processing
debug_session();
error_log("Dashboard - Starting page load");

// Additional debug for admin status
error_log("Is admin check: " . (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? 'true' : 'false'));

// Include admin check
require_once 'admin_check.php';

// If we get here, authentication was successful
error_log("Dashboard - Admin authentication successful");

// If we get here, the user is an admin
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitQuest</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <header>
        <div class="logo">
            <span class="material-symbols-outlined">fitness_center</span>
            <h1>FitQuest Admin</h1>
        </div>
        <nav class="desktop-nav">
            <ul>
                <li class="active"><a href="dashboard.php">Admin Dashboard</a></li>
                <li><a href="../challenges.php">Challenges</a></li>
                <li><a href="../leaderboard.php">Leaderboard</a></li>
                <li><a href="../quiz.php">Health Quiz</a></li>
                <li><a href="contact_messages.php">Messages</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <div class="user-avatar" id="user-menu-btn">
                <img src="https://via.placeholder.com/40" alt="Admin avatar">
            </div>
            <div class="user-dropdown" id="user-dropdown">
                <ul>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="../api/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main>
        <section class="page-header">
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </section>

        <div class="admin-stats">
            <!-- Admin stats will go here -->
            <div class="stat-card">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <p class="stat-value">
                        <?php
                        // Connect to database
                        require_once '../includes/db_connect.php';
                        $result = $conn->query("SELECT COUNT(*) as total FROM users");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>
            
            <!-- Add more stat cards as needed -->
        </div>

        <div class="admin-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="users.php" class="admin-action-btn">
                    <span class="material-symbols-outlined">manage_accounts</span>
                    Manage Users
                </a>
                <a href="contact_messages.php" class="admin-action-btn">
                    <span class="material-symbols-outlined">mail</span>
                    View Messages
                </a>
                <!-- Add more action buttons as needed -->
            </div>
        </div>
    </main>

    <script>
        // Setup user dropdown
        document.getElementById('user-menu-btn').addEventListener('click', function() {
            document.getElementById('user-dropdown').classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.remove('active');
            }
        });
    </script>
</body>
</html>