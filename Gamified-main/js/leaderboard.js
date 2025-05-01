// // Leaderboard data
// const leaderboardData = [
//     { rank: 1, name: 'Alex J.', points: 1250, avatar: 'https://via.placeholder.com/40' },
//     { rank: 2, name: 'Sarah W.', points: 1120, avatar: 'https://via.placeholder.com/40' },
//     { rank: 3, name: 'Michael B.', points: 980, avatar: 'https://via.placeholder.com/40' },
//     { rank: 4, name: 'Jessica D.', points: 870, avatar: 'https://via.placeholder.com/40', isCurrentUser: true },
//     { rank: 5, name: 'David M.', points: 760, avatar: 'https://via.placeholder.com/40' },
//     { rank: 6, name: 'Emily W.', points: 650, avatar: 'https://via.placeholder.com/40' },
//     { rank: 7, name: 'Robert T.', points: 540, avatar: 'https://via.placeholder.com/40' },
//     { rank: 8, name: 'Jennifer M.', points: 430, avatar: 'https://via.placeholder.com/40' },
//     { rank: 9, name: 'Christopher L.', points: 320, avatar: 'https://via.placeholder.com/40' },
//     { rank: 10, name: 'Amanda W.', points: 210, avatar: 'https://via.placeholder.com/40' }
// ];

// // Monthly leaderboard data
// const monthlyLeaderboardData = [
//     { rank: 1, name: 'Sarah W.', points: 4250, avatar: 'https://via.placeholder.com/40' },
//     { rank: 2, name: 'Alex J.', points: 3980, avatar: 'https://via.placeholder.com/40' },
//     { rank: 3, name: 'David M.', points: 3720, avatar: 'https://via.placeholder.com/40' },
//     { rank: 4, name: 'Michael B.', points: 3450, avatar: 'https://via.placeholder.com/40' },
//     { rank: 5, name: 'Jessica D.', points: 3120, avatar: 'https://via.placeholder.com/40', isCurrentUser: true },
//     { rank: 6, name: 'Emily W.', points: 2890, avatar: 'https://via.placeholder.com/40' },
//     { rank: 7, name: 'Robert T.', points: 2540, avatar: 'https://via.placeholder.com/40' },
//     { rank: 8, name: 'Jennifer M.', points: 2230, avatar: 'https://via.placeholder.com/40' },
//     { rank: 9, name: 'Christopher L.', points: 1920, avatar: 'https://via.placeholder.com/40' },
//     { rank: 10, name: 'Amanda W.', points: 1650, avatar: 'https://via.placeholder.com/40' }
// ];

// // All-time leaderboard data
// const allTimeLeaderboardData = [
//     { rank: 1, name: 'David M.', points: 12450, avatar: 'https://via.placeholder.com/40' },
//     { rank: 2, name: 'Sarah W.', points: 11280, avatar: 'https://via.placeholder.com/40' },
//     { rank: 3, name: 'Alex J.', points: 10750, avatar: 'https://via.placeholder.com/40' },
//     { rank: 4, name: 'Michael B.', points: 9320, avatar: 'https://via.placeholder.com/40' },
//     { rank: 5, name: 'Emily W.', points: 8640, avatar: 'https://via.placeholder.com/40' },
//     { rank: 6, name: 'Jessica D.', points: 7890, avatar: 'https://via.placeholder.com/40', isCurrentUser: true },
//     { rank: 7, name: 'Robert T.', points: 6540, avatar: 'https://via.placeholder.com/40' },
//     { rank: 8, name: 'Jennifer M.', points: 5230, avatar: 'https://via.placeholder.com/40' },
//     { rank: 9, name: 'Christopher L.', points: 4120, avatar: 'https://via.placeholder.com/40' },
//     { rank: 10, name: 'Amanda W.', points: 3450, avatar: 'https://via.placeholder.com/40' }
// ];

// // Initialize leaderboard page
// document.addEventListener('DOMContentLoaded', function() {
//     const leaderboardContainer = document.getElementById('full-leaderboard');
    
//     if (leaderboardContainer) {
//         // Populate initial leaderboard (weekly)
//         populateLeaderboard(leaderboardContainer, leaderboardData);
        
//         // Update top performers
//         updateTopPerformers(leaderboardData);
        
//         // Setup period filter buttons
//         const periodBtns = document.querySelectorAll('.filter-btn[data-period]');
        
//         periodBtns.forEach(btn => {
//             btn.addEventListener('click', function() {
//                 const period = this.getAttribute('data-period');
                
//                 // Get appropriate data based on period
//                 let data;
//                 if (period === 'weekly') {
//                     data = leaderboardData;
//                 } else if (period === 'monthly') {
//                     data = monthlyLeaderboardData;
//                 } else if (period === 'alltime') {
//                     data = allTimeLeaderboardData;
//                 }
                
//                 // Clear and repopulate leaderboard
//                 leaderboardContainer.innerHTML = '';
//                 populateLeaderboard(leaderboardContainer, data);
                
//                 // Update top performers
//                 updateTopPerformers(data);
//             });
//         });
//     }
// });

// // Populate leaderboard
// function populateLeaderboard(container, data) {
//     data.forEach(user => {
//         const row = document.createElement('div');
//         row.className = `leaderboard-row ${user.isCurrentUser ? 'current-user' : ''}`;
        
//         row.innerHTML = `
//             <div class="leaderboard-rank">${user.rank}</div>
//             <div class="leaderboard-user">
//                 <img src="${user.avatar}" alt="${user.name}">
//                 <span>${user.name} ${user.isCurrentUser ? '<span style="background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px;">You</span>' : ''}</span>
//             </div>
//             <div class="leaderboard-points">${user.points.toLocaleString()}</div>
//         `;
        
//         container.appendChild(row);
//     });
// }

// // Update top performers
// function updateTopPerformers(data) {
//     // Get top 3 users
//     const top3 = data.slice(0, 3);
    
//     // Update first place
//     const firstPlace = document.querySelector('.first-place');
//     if (firstPlace) {
//         firstPlace.querySelector('.user-name').textContent = top3[0].name;
//         firstPlace.querySelector('.user-points').textContent = `${top3[0].points.toLocaleString()} pts`;
//         firstPlace.querySelector('img').src = top3[0].avatar;
//     }
    
//     // Update second place
//     const secondPlace = document.querySelector('.second-place');
//     if (secondPlace) {
//         secondPlace.querySelector('.user-name').textContent = top3[1].name;
//         secondPlace.querySelector('.user-points').textContent = `${top3[1].points.toLocaleString()} pts`;
//         secondPlace.querySelector('img').src = top3[1].avatar;
//     }
    
//     // Update third place
//     const thirdPlace = document.querySelector('.third-place');
//     if (thirdPlace) {
//         thirdPlace.querySelector('.user-name').textContent = top3[2].name;
//         thirdPlace.querySelector('.user-points').textContent = `${top3[2].points.toLocaleString()} pts`;
//         thirdPlace.querySelector('img').src = top3[2].avatar;
//     }
// }