// Rewards data
const rewardsData = [
    {
        id: 1,
        title: '$10 Gift Card',
        description: 'Redeem for a $10 gift card at your favorite sports store',
        points: 1000,
        image: 'https://via.placeholder.com/300x160'
    },
    {
        id: 2,
        title: 'Premium Membership',
        description: '1 month of premium membership with exclusive features',
        points: 2000,
        image: 'https://via.placeholder.com/300x160'
    },
    {
        id: 3,
        title: 'Fitness Tracker',
        description: 'Discount coupon for a new fitness tracker',
        points: 5000,
        image: 'https://via.placeholder.com/300x160'
    },
    {
        id: 4,
        title: 'Workout Plan',
        description: 'Custom workout plan created by professional trainers',
        points: 1500,
        image: 'https://via.placeholder.com/300x160'
    },
    {
        id: 5,
        title: 'Nutrition Consultation',
        description: '30-minute nutrition consultation with a dietitian',
        points: 3000,
        image: 'https://via.placeholder.com/300x160'
    },
    {
        id: 6,
        title: 'Workout Gear',
        description: 'Branded workout t-shirt and water bottle',
        points: 1200,
        image: 'https://via.placeholder.com/300x160'
    }
];

// Achievements data
const achievementsData = [
    {
        id: 1,
        title: 'Early Bird',
        description: 'Complete 5 morning workouts',
        progress: 3,
        total: 5,
        icon: 'wb_sunny'
    },
    {
        id: 2,
        title: 'Marathon Runner',
        description: 'Run a total of 42km',
        progress: 28,
        total: 42,
        icon: 'directions_run'
    },
    {
        id: 3,
        title: 'Consistency King',
        description: 'Log activity for 30 consecutive days',
        progress: 14,
        total: 30,
        icon: 'calendar_month'
    },
    {
        id: 4,
        title: 'Step Master',
        description: 'Reach 10,000 steps for 10 days',
        progress: 7,
        total: 10,
        icon: 'footprint'
    },
    {
        id: 5,
        title: 'Hydration Hero',
        description: 'Log 8 glasses of water for 7 days',
        progress: 5,
        total: 7,
        icon: 'water_drop'
    },
    {
        id: 6,
        title: 'Strength Champion',
        description: 'Complete 20 strength workouts',
        progress: 12,
        total: 20,
        icon: 'fitness_center'
    }
];

// Initialize rewards page
document.addEventListener('DOMContentLoaded', function() {
    const rewardsContainer = document.getElementById('rewards-container');
    const achievementsContainer = document.getElementById('achievements-container');
    
    if (rewardsContainer && achievementsContainer) {
        // Get user points
        const userPoints = parseInt(document.getElementById('rewards-points').textContent.replace(',', ''));
        
        // Populate rewards
        populateRewards(rewardsContainer, rewardsData, userPoints);
        
        // Populate achievements
        populateAchievements(achievementsContainer, achievementsData);
    }
});

// Populate rewards
function populateRewards(container, rewards, userPoints) {
    rewards.forEach(reward => {
        const card = document.createElement('div');
        card.className = 'reward-card';
        
        // Check if user has enough points
        const canRedeem = userPoints >= reward.points;
        
        card.innerHTML = `
            <div class="reward-image">
                <img src="${reward.image}" alt="${reward.title}">
            </div>
            <div class="reward-content">
                <h3>${reward.title}</h3>
                <p>${reward.description}</p>
                <div class="reward-footer">
                    <div class="reward-points">${reward.points.toLocaleString()} points</div>
                    <button class="reward-btn" ${!canRedeem ? 'disabled' : ''}>Redeem</button>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

// Populate achievements
function populateAchievements(container, achievements) {
    achievements.forEach(achievement => {
        const card = document.createElement('div');
        card.className = 'achievement-card';
        
        // Calculate progress percentage
        const progressPercentage = (achievement.progress / achievement.total) * 100;
        
        card.innerHTML = `
            <div class="achievement-header">
                <div class="achievement-icon">
                    <span class="material-symbols-outlined">${achievement.icon}</span>
                </div>
                <div class="achievement-info">
                    <h3>${achievement.title}</h3>
                    <p>${achievement.description}</p>
                </div>
            </div>
            <div class="achievement-progress">
                <div class="achievement-progress-text">
                    <span>Progress</span>
                    <span>${achievement.progress}/${achievement.total}</span>
                </div>
                <div class="achievement-progress-bar">
                    <div class="achievement-progress-fill" style="width: ${progressPercentage}%"></div>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}