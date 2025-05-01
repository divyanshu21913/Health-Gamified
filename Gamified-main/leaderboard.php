<?php
// Start session
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - FitQuest</title>
    <link rel="stylesheet" href="css/leaderb.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <h2>Leaderboard</h2>
            <p>See how you rank against other health enthusiasts!</p>
        </section>

        <section class="leaderboard-full">
            <div class="top-performers" id="top-performers">
                <!-- Top 3 performers will be populated by JavaScript -->
            </div>

            <div class="leaderboard-table">
                <div class="leaderboard-header">
                    <span>Rank</span>
                    <span>User</span>
                    <span>Points</span>
                </div>
                <div class="leaderboard-body" id="full-leaderboard">
                    <!-- Leaderboard entries will be populated by JavaScript -->
                </div>
            </div>
        </section>
    </main>

    <nav class="mobile-nav">
        <a href="index.php">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="challenges.php">
            <span class="material-symbols-outlined">emoji_events</span>
            <span>Challenges</span>
        </a>
        <a href="leaderboard.php" class="active">
            <span class="material-symbols-outlined">leaderboard</span>
            <span>Leaderboard</span>
        </a>
        <a href="rewards.php">
            <span class="material-symbols-outlined">redeem</span>
            <span>Rewards</span>
        </a>
    </nav>

    <script>
        // Fetch and populate leaderboard data
        document.addEventListener('DOMContentLoaded', function() {
            fetch('api/get_leaderboard.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate top performers
                        const topPerformers = document.getElementById('top-performers');
                        const top3 = data.leaderboard.slice(0, 3);
                        
                        if (top3.length >= 3) {
                            topPerformers.innerHTML = `
                                <div class="performer second-place">
                                    <div class="rank">2</div>
                                    <div class="user-avatar">
                                        <img src="https://via.placeholder.com/80" alt="Second place">
                                    </div>
                                    <div class="user-name">${top3[1].username}</div>
                                    <div class="user-points">${top3[1].points} pts</div>
                                </div>
                                <div class="performer first-place">
                                    <div class="rank">1</div>
                                    <div class="user-avatar">
                                        <img src="https://via.placeholder.com/100" alt="First place">
                                    </div>
                                    <div class="user-name">${top3[0].username}</div>
                                    <div class="user-points">${top3[0].points} pts</div>
                                </div>
                                <div class="performer third-place">
                                    <div class="rank">3</div>
                                    <div class="user-avatar">
                                        <img src="https://via.placeholder.com/80" alt="Third place">
                                    </div>
                                    <div class="user-name">${top3[2].username}</div>
                                    <div class="user-points">${top3[2].points} pts</div>
                                </div>
                            `;
                        }
                        
                        // Populate leaderboard
                        const leaderboardBody = document.getElementById('full-leaderboard');
                        leaderboardBody.innerHTML = '';
                        
                        data.leaderboard.forEach(user => {
                            const row = document.createElement('div');
                            row.className = `leaderboard-row ${user.id == <?php echo $_SESSION['user_id']; ?> ? 'current-user' : ''}`;
                            
                            row.innerHTML = `
                                <div class="leaderboard-rank">${user.rank}</div>
                                <div class="leaderboard-user">
                                    <img src="https://via.placeholder.com/40" alt="${user.username}">
                                    <span>${user.username} ${user.id == <?php echo $_SESSION['user_id']; ?> ? '<span style="background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px;">You</span>' : ''}</span>
                                </div>
                                <div class="leaderboard-points">${user.points}</div>
                            `;
                            
                            leaderboardBody.appendChild(row);
                        });
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
    <script src="js/dropdark.js"></script>
</body>
</html>