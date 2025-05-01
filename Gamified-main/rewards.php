<?php
include 'includes/session_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards - FitQuest</title>
    <link rel="stylesheet" href="css/r.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <h2>Rewards</h2>
            <div class="points-display">
                <span class="points-value" id="rewards-points">1,250</span>
                <span class="points-label">Points Available</span>
            </div>
        </section>

        <section class="rewards-tabs">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="shop">Rewards Shop</button>
                <button class="tab-btn" data-tab="achievements">Achievements</button>
            </div>
            
            <div class="tab-content active" id="shop-tab">
                <div class="rewards-grid" id="rewards-container">
                    <!-- Rewards will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="tab-content" id="achievements-tab">
                <div class="achievements-grid" id="achievements-container">
                    <!-- Achievements will be populated by JavaScript -->
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
        <a href="leaderboard.php">
            <span class="material-symbols-outlined">leaderboard</span>
            <span>Leaderboard</span>
        </a>
        <a href="rewards.php" class="active">
            <span class="material-symbols-outlined">redeem</span>
            <span>Rewards</span>
        </a>
    </nav>

    <script src="js/script.js"></script>
    <script src="js/rs.js"></script>
    <script src="js/rewards.js"></script>
    <script src="js/dropdark.js"></script>
</body>
</html>