// Challenges data
const challengesData = [
    {
        id: 1,
        title: 'Morning Run Challenge',
        description: 'Run 5km every morning for a week',
        difficulty: 'medium',
        reward: 50,
        progress: 2,
        total: 5,
        daysLeft: 3,
        status: 'active'
    },
    {
        id: 2,
        title: '10,000 Steps',
        description: 'Reach 10,000 steps daily for 5 consecutive days',
        difficulty: 'easy',
        reward: 30,
        progress: 3,
        total: 5,
        daysLeft: 2,
        status: 'active'
    },
    {
        id: 3,
        title: 'Cycling Weekend',
        description: 'Cycle 20km this weekend',
        difficulty: 'hard',
        reward: 75,
        progress: 0,
        total: 1,
        daysLeft: 2,
        status: 'available'
    },
    {
        id: 4,
        title: 'Healthy Eating',
        description: 'Log your meals for 7 consecutive days',
        difficulty: 'easy',
        reward: 40,
        progress: 7,
        total: 7,
        daysLeft: 0,
        status: 'completed'
    },
    {
        id: 5,
        title: 'Strength Training',
        description: 'Complete 3 strength workouts this week',
        difficulty: 'medium',
        reward: 60,
        progress: 1,
        total: 3,
        daysLeft: 4,
        status: 'active'
    },
    {
        id: 6,
        title: 'Marathon Prep',
        description: 'Run 50km in total this month',
        difficulty: 'hard',
        reward: 100,
        progress: 0,
        total: 1,
        daysLeft: 14,
        status: 'available'
    },
    {
        id: 7,
        title: 'Yoga Challenge',
        description: 'Complete 10 yoga sessions',
        difficulty: 'medium',
        reward: 45,
        progress: 10,
        total: 10,
        daysLeft: 0,
        status: 'completed'
    },
    {
        id: 8,
        title: 'Hydration Hero',
        description: 'Drink 8 glasses of water daily for 10 days',
        difficulty: 'easy',
        reward: 35,
        progress: 0,
        total: 10,
        daysLeft: 10,
        status: 'available'
    },
    {
        id: 9,
        title: 'Sleep Well',
        description: 'Get 8 hours of sleep for 7 consecutive nights',
        difficulty: 'medium',
        reward: 50,
        progress: 0,
        total: 7,
        daysLeft: 7,
        status: 'available'
    }
];

// Initialize challenges page
document.addEventListener('DOMContentLoaded', function() {
    const challengesContainer = document.getElementById('challenges-container');
    
    if (challengesContainer) {
        populateChallenges(challengesContainer, challengesData);
        
        // Setup filter buttons
        const filterBtns = document.querySelectorAll('.filter-btn');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Filter challenges
                let filteredChallenges;
                if (filter === 'all') {
                    filteredChallenges = challengesData;
                } else {
                    filteredChallenges = challengesData.filter(challenge => challenge.status === filter);
                }
                
                // Clear and repopulate challenges
                challengesContainer.innerHTML = '';
                populateChallenges(challengesContainer, filteredChallenges);
            });
        });
    }
});

// Populate challenges
function populateChallenges(container, challenges) {
    challenges.forEach(challenge => {
        const card = document.createElement('div');
        card.className = 'challenge-card';
        
        // Calculate progress percentage
        const progressPercentage = (challenge.progress / challenge.total) * 100;
        
        // Create button text and class based on status
        let buttonText;
        let buttonClass = '';
        let buttonType = '';
        
        if (challenge.status === 'completed') {
            buttonText = 'Completed';
            buttonClass = 'disabled';
            buttonType = 'btn-outline';
        } else if (challenge.status === 'active') {
            buttonText = 'In Progress';
            buttonType = 'btn-outline';
        } else {
            buttonText = 'Join Challenge';
            buttonType = 'btn-primary';
        }
        
        card.innerHTML = `
            <div class="challenge-header">
                <h3>${challenge.title}</h3>
                <span class="challenge-badge ${challenge.difficulty}">${challenge.difficulty.charAt(0).toUpperCase() + challenge.difficulty.slice(1)}</span>
            </div>
            <p>${challenge.description}</p>
            <div class="challenge-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${progressPercentage}%"></div>
                </div>
                <div class="progress-text">${challenge.progress}/${challenge.total} ${challenge.total > 1 ? 'days' : 'complete'}</div>
            </div>
            <div class="challenge-meta">
                <div class="challenge-reward">
                    <span class="material-symbols-outlined">emoji_events</span>
                    <span>${challenge.reward} points</span>
                </div>
                <div class="challenge-time">
                    <span class="material-symbols-outlined">schedule</span>
                    <span>${challenge.daysLeft} days left</span>
                </div>
            </div>
            <div class="challenge-actions">
                <button class="btn ${buttonType} ${buttonClass}" style="width: 100%;" ${challenge.status === 'completed' ? 'disabled' : ''}>${buttonText}</button>
            </div>
        `;
        
        container.appendChild(card);
        
        // Add event listeners to buttons
        const button = card.querySelector('.btn');
        button.addEventListener('click', function() {
            if (challenge.status === 'available') {
                // Join Challenge functionality
                challenge.status = 'active';
                challenge.progress = 0;
                alert(`You've joined the "${challenge.title}" challenge!`);
                
                // Refresh the challenges display
                container.innerHTML = '';
                populateChallenges(container, challenges);
            } else if (challenge.status === 'active') {
                // In Progress functionality - show progress update modal/form
                alert(`Track your progress for "${challenge.title}"`);
                
                // For demo purposes, increment progress
                if (challenge.progress < challenge.total) {
                    challenge.progress += 1;
                    
                    // Check if challenge is completed
                    if (challenge.progress >= challenge.total) {
                        challenge.status = 'completed';
                        alert(`Congratulations! You've completed the "${challenge.title}" challenge!`);
                    }
                    
                    // Refresh the challenges display
                    container.innerHTML = '';
                    populateChallenges(container, challenges);
                }
            }
        });
    });
}
