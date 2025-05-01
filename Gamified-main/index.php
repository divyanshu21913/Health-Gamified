<?php
session_start();
// If user is not logged in, redirect to login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Include database connection and get user data
require_once('includes/db_connect.php');

// Get header user data
$header_query = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
$header_query->bind_param("i", $_SESSION['user_id']);
$header_query->execute();
$header_user = $header_query->get_result()->fetch_assoc();
$header_query->close();

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$points = isset($_SESSION['points']) ? $_SESSION['points'] : 0;

// Get today's fitness data if available
$today = date('Y-m-d');
$fitness_query = $conn->prepare("SELECT steps, calories, active_minutes FROM fitness_data WHERE user_id = ? AND date = ? LIMIT 1");
$fitness_query->bind_param("is", $user_id, $today);
$fitness_query->execute();
$result = $fitness_query->get_result();

$steps = 0;
$calories = 0;
$active_minutes = 0;

if ($result->num_rows > 0) {
    $fitness_data = $result->fetch_assoc();
    $steps = $fitness_data['steps'];
    $calories = $fitness_data['calories'];
    $active_minutes = $fitness_data['active_minutes'];
}

// Calculate progress percentages
$steps_percentage = min(($steps / 10000) * 100, 100);
$calories_percentage = min(($calories / 2000) * 100, 100);
$active_minutes_percentage = min(($active_minutes / 60) * 100, 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitQuest - Gamified Health Platform</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="css/dark-theme.css">
    <link rel="stylesheet" href="css/fitness.css">
</head>
<body class="dark-theme">
    
    <header>
        <a href="index.php" class="logo">
            <span class="material-symbols-outlined">fitness_center</span>
            <span>FitQuest</span>
        </a>
        
        <nav class="desktop-nav">
            <ul>
                <li<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? ' class="active"' : ''; ?>><a href="index.php">Dashboard</a></li>
                <li<?php echo basename($_SERVER['PHP_SELF']) == 'challenges.php' ? ' class="active"' : ''; ?>><a href="challenges.php">Challenges</a></li>
                <li<?php echo basename($_SERVER['PHP_SELF']) == 'leaderboard.php' ? ' class="active"' : ''; ?>><a href="leaderboard.php">Leaderboard</a></li>
                <li<?php echo basename($_SERVER['PHP_SELF']) == 'rewards.php' ? ' class="active"' : ''; ?>><a href="rewards.php">Rewards</a></li>
                <li<?php echo basename($_SERVER['PHP_SELF']) == 'quiz.php' ? ' class="active"' : ''; ?>><a href="quiz.php">Quiz</a></li>
                <li<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? ' class="active"' : ''; ?>><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
        
        <div class="user-controls">
            <div class="controls-right">
                <button id="theme-toggle" class="icon-button">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>
                
                <div class="notifications">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="notification-badge">2</span>
                </div>
                
                <div class="user-menu">
                    <button id="user-menu-btn" class="user-avatar">
                        <img src="<?php echo htmlspecialchars($header_user['profile_image'] ? 'img/profile_images/' . $header_user['profile_image'] : 'https://via.placeholder.com/40'); ?>" alt="User avatar">
                    </button>
                    <div id="user-dropdown" class="dropdown-menu">
                        <a href="profile.php">Profile</a>
                        <a href="settings.php">Settings</a>
                        <a href="api/logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="welcome-section">
            <div class="welcome-text">
                <h2>Welcome back, <span><?php echo htmlspecialchars($username); ?></span>!</h2>
                <p>You're making great progress. Keep it up!</p>
            </div>
            <div class="points-display">
                <span class="points-value"><?php echo number_format($points); ?></span>
                <span class="points-label">Points</span>
            </div>
        </section>

        <section class="progress-section">
            <h2>Today's Progress</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">footprint</span>
                    </div>
                    <div class="stat-info">
                        <h3>Steps</h3>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $steps_percentage; ?>%"></div>
                            </div>
                            <div class="progress-text">
                                <span id="steps-count"><?php echo number_format($steps); ?></span>
                                <span class="progress-target">/ 10,000</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">local_fire_department</span>
                    </div>
                    <div class="stat-info">
                        <h3>Calories</h3>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $calories_percentage; ?>%"></div>
                            </div>
                            <div class="progress-text">
                                <span id="calories-count"><?php echo number_format($calories); ?></span>
                                <span class="progress-target">/ 2,000</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">timer</span>
                    </div>
                    <div class="stat-info">
                        <h3>Active Minutes</h3>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $active_minutes_percentage; ?>%"></div>
                            </div>
                            <div class="progress-text">
                                <span id="active-minutes"><?php echo $active_minutes; ?></span>
                                <span class="progress-target">/ 60</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fitness Data Input Form -->
            <div class="fitness-input-section">
                <form id="fitness-form" class="fitness-form">
                    <div class="form-group">
                        <label for="fitness-date">Date</label>
                        <input type="date" id="fitness-date" name="date" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="steps-input">Steps</label>
                        <input type="number" id="steps-input" name="steps" min="0" value="<?php echo $steps; ?>" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label for="calories-input">Calories Burned</label>
                        <input type="number" id="calories-input" name="calories" min="0" value="<?php echo $calories; ?>" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label for="active-minutes-input">Active Minutes</label>
                        <input type="number" id="active-minutes-input" name="active_minutes" min="0" value="<?php echo $active_minutes; ?>" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <button type="submit">Save Progress</button>
                    </div>
                </form>
            </div>

            <!-- Daily History Table -->
            <div class="fitness-history-section">
                <div class="section-header">
                    <h2>Daily History</h2>
                </div>
                <div class="table-wrapper">
                    <table id="fitness-history-table" class="fitness-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Steps</th>
                                <th>Calories</th>
                                <th>Active Minutes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="challenges-section">
            <div class="section-header">
                <h2>Active Challenges</h2>
                <a href="challenges.php" class="view-all">View All</a>
            </div>
            <div class="challenges-grid">
                <div class="challenge-card">
                    <div class="challenge-header">
                        <h3>Morning Run Challenge</h3>
                        <span class="challenge-badge medium">Medium</span>
                    </div>
                    <p>Run 5km every morning for a week</p>
                    <div class="challenge-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 40%"></div>
                        </div>
                        <div class="progress-text">2/5 days</div>
                    </div>
                    <div class="challenge-meta">
                        <div class="challenge-reward">
                            <span class="material-symbols-outlined">emoji_events</span>
                            <span>50 points</span>
                        </div>
                        <div class="challenge-time">
                            <span class="material-symbols-outlined">schedule</span>
                            <span>3 days left</span>
                        </div>
                    </div>
                </div>
                <div class="challenge-card">
                    <div class="challenge-header">
                        <h3>10,000 Steps</h3>
                        <span class="challenge-badge easy">Easy</span>
                    </div>
                    <p>Reach 10,000 steps daily for 5 consecutive days</p>
                    <div class="challenge-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 60%"></div>
                        </div>
                        <div class="progress-text">3/5 days</div>
                    </div>
                    <div class="challenge-meta">
                        <div class="challenge-reward">
                            <span class="material-symbols-outlined">emoji_events</span>
                            <span>30 points</span>
                        </div>
                        <div class="challenge-time">
                            <span class="material-symbols-outlined">schedule</span>
                            <span>2 days left</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
    </main>

    <nav class="mobile-nav">
        <a href="index.php" class="active">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="challenges.php">
            <span class="material-symbols-outlined">emoji_events</span>
            <span>Challenges</span>
        </a>
        <a href="leaderboard.php">
            <span class="material-symbols-outlined">leaderboard</span>
            <span>Leaderboard</span>
        </a>
        <a href="rewards.php">
            <span class="material-symbols-outlined">redeem</span>
            <span>Rewards</span>
        </a>
        <a href="quiz.php">
            <span class="material-symbols-outlined">quiz</span>
            <span>Quiz</span>
        </a>
    </nav>

    <script src="js/theme-toggle.js"></script>
    <script src="js/header.js"></script>
    <script src="js/fitness-tracker.js"></script>
</body>
</html>
