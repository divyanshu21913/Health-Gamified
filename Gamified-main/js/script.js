// User data is loaded from PHP

// Initialize the app
document.addEventListener('DOMContentLoaded', function() {
    // Update user data in UI
    updateUserData();
    
    // Setup user dropdown
    setupUserDropdown();
    
    // Setup leaderboard if on dashboard
    if (document.getElementById('leaderboard-list')) {
        populateDashboardLeaderboard();
    }
});

// Update user data in UI
function updateUserData() {
    // Update user name
    const userNameElement = document.getElementById('user-name');
    if (userNameElement) {
        userNameElement.textContent = userData.name;
    }
    
    // Update points
    const userPointsElements = document.querySelectorAll('#user-points, #rewards-points');
    userPointsElements.forEach(element => {
        if (element) {
            element.textContent = userData.points.toLocaleString();
        }
    });
    
    // Update stats
    const stepsElement = document.getElementById('steps-count');
    if (stepsElement) {
        stepsElement.textContent = userData.steps.toLocaleString();
    }
    
    const caloriesElement = document.getElementById('calories-count');
    if (caloriesElement) {
        caloriesElement.textContent = userData.calories.toLocaleString();
    }
    
    const activeMinutesElement = document.getElementById('active-minutes');
    if (activeMinutesElement) {
        activeMinutesElement.textContent = userData.activeMinutes.toLocaleString();
    }
}

// Setup user dropdown
function setupUserDropdown() {
    console.log('Setting up user dropdown...');
    const userMenuBtn = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userMenuBtn && userDropdown) {
        console.log('Found menu elements');
        
        userMenuBtn.addEventListener('click', function(e) {
            console.log('User menu clicked');
            e.preventDefault();
            e.stopPropagation(); // Stop event from bubbling up
            
            // Force display block before toggling class for animation
            if (!userDropdown.classList.contains('active')) {
                userDropdown.style.display = 'block';
                setTimeout(() => userDropdown.classList.add('active'), 0);
            } else {
                userDropdown.classList.remove('active');
                setTimeout(() => userDropdown.style.display = 'none', 300); // Match transition time
            }
            console.log('Dropdown state:', userDropdown.classList.contains('active'));
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            console.log('Document clicked, checking if should close dropdown');
            if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.remove('active');
                setTimeout(() => userDropdown.style.display = 'none', 300);
            }
        });
    } else {
        console.error('Menu elements not found:',
            'userMenuBtn:', userMenuBtn,
            'userDropdown:', userDropdown
        );
    }
}

// Populate dashboard leaderboard
function populateDashboardLeaderboard() {
    const leaderboardData = [
        { rank: 1, name: userData.name, points: userData.points, avatar: 'https://via.placeholder.com/32' },
        { rank: 2, name: 'Sarah W.', points: Math.floor(userData.points * 0.9), avatar: 'https://via.placeholder.com/32' },
        { rank: 3, name: 'Michael B.', points: Math.floor(userData.points * 0.8), avatar: 'https://via.placeholder.com/32' },
        { rank: 4, name: 'Jessica D.', points: Math.floor(userData.points * 0.7), avatar: 'https://via.placeholder.com/32' },
        { rank: 5, name: 'David M.', points: Math.floor(userData.points * 0.6), avatar: 'https://via.placeholder.com/32' }
    ];
    
    const leaderboardList = document.getElementById('leaderboard-list');
    
    leaderboardData.forEach(user => {
        const row = document.createElement('div');
        row.className = `leaderboard-row ${user.rank === 1 ? 'current-user' : ''}`;
        
        row.innerHTML = `
            <div class="leaderboard-rank">${user.rank}</div>
            <div class="leaderboard-user">
                <img src="${user.avatar}" alt="${user.name}">
                <span>${user.name}</span>
            </div>
            <div class="leaderboard-points">${user.points.toLocaleString()}</div>
        `;
        
        leaderboardList.appendChild(row);
    });
}

// Setup tabs functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
});

// Setup filter buttons
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all filter buttons
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter functionality would be implemented here
            // For now, just a visual change
        });
    });
});