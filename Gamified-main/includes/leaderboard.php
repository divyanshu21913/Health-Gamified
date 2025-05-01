<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - FitQuest</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <header>
        <div class="logo">
            <span class="material-symbols-outlined">fitness_center</span>
            <h1>FitQuest</h1>
        </div>
        <nav class="desktop-nav">
            <ul>
                <li><a href="index.html">Dashboard</a></li>
                <li><a href="challenges.html">Challenges</a></li>
                <li class="active"><a href="leaderboard.html">Leaderboard</a></li>
                <li><a href="rewards.html">Rewards</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <button id="notifications-btn" class="icon-btn">
                <span class="material-symbols-outlined">notifications</span>
                <span class="notification-badge"></span>
            </button>
            <div class="user-avatar" id="user-menu-btn">
                <img src="https://via.placeholder.com/40" alt="User avatar">
            </div>
            <div class="user-dropdown" id="user-dropdown">
                <ul>
                    <li><a href="profile.html">Profile</a></li>
                    <li><a href="settings.html">Settings</a></li>
                    <li><a href="#" id="logout-btn">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main>
        <section class="page-header">
            <h2>Leaderboard</h2>
            <div class="filter-controls">
                <button class="filter-btn active" data-period="weekly">Weekly</button>
                <button class="filter-btn" data-period="monthly">Monthly</button>
                <button class="filter-btn" data-period="alltime">All Time</button>
            </div>
        </section>

        <section class="leaderboard-full">
            <div class="top-performers">
                <div class="performer second-place">
                    <div class="rank">2</div>
                    <div class="user-avatar">
                        <img src="https://via.placeholder.com/80" alt="Second place">
                    </div>
                    <div class="user-name">Sarah W.</div>
                    <div class="user-points">1,120 pts</div>
                </div>
                <div class="performer first-place">
                    <div class="rank">1</div>
                    <div class="user-avatar">
                        <img src="https://via.placeholder.com/100" alt="First place">
                    </div>
                    <div class="user-name">Alex J.</div>
                    <div class="user-points">1,250 pts</div>
                </div>
                <div class="performer third-place">
                    <div class="rank">3</div>
                    <div class="user-avatar">
                        <img src="https://via.placeholder.com/80" alt="Third place">
                    </div>
                    <div class="user-name">Michael B.</div>
                    <div class="user-points">980 pts</div>
                </div>
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
        <a href="index.html">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="challenges.html">
            <span class="material-symbols-outlined">emoji_events</span>
            <span>Challenges</span>
        </a>
        <a href="leaderboard.html" class="active">
            <span class="material-symbols-outlined">leaderboard</span>
            <span>Leaderboard</span>
        </a>
        <a href="rewards.html">
            <span class="material-symbols-outlined">redeem</span>
            <span>Rewards</span>
        </a>
    </nav>

    <script src="script.js"></script>
    <script src="leaderboard.js"></script>
</body>
</html>