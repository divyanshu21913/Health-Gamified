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
    <title>Health Quiz - FitQuest</title>
    <link rel="stylesheet" href="css/q.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <h2>Health Knowledge Quiz</h2>
            <p>Test your health knowledge with these 10 questions. Each correct answer earns you 10 points!</p>
        </section>

        <section class="quiz-container">
            <div id="quiz-loading">Loading questions...</div>
            
            <div id="quiz-questions" style="display: none;">
                <!-- Questions will be populated by JavaScript -->
            </div>
            
            <div id="quiz-results" style="display: none;">
                <h3>Quiz Results</h3>
                <div class="results-content">
                    <p>You scored <span id="score">0</span> out of <span id="total">10</span> questions correctly!</p>
                    <p>You earned <span id="points-earned">0</span> points!</p>
                </div>
                <button id="view-leaderboard" class="auth-btn">View Leaderboard</button>
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
        <a href="rewards.php">
            <span class="material-symbols-outlined">redeem</span>
            <span>Rewards</span>
        </a>
    </nav>

    <script src="js/header.js"></script>
    <script>
        // Initialize quiz functionality once DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const quizLoading = document.getElementById('quiz-loading');
            const quizQuestions = document.getElementById('quiz-questions');
            const quizResults = document.getElementById('quiz-results');
            
            // Fetch questions from API
            fetch('api/get_questions.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        quizLoading.style.display = 'none';
                        quizQuestions.style.display = 'block';
                        
                        // Create quiz form
                        const quizForm = document.createElement('form');
                        quizForm.id = 'quiz-form';
                        
                        // Add questions to form
                        data.questions.forEach((question, index) => {
                            const questionDiv = document.createElement('div');
                            questionDiv.className = 'quiz-question';
                            
                            questionDiv.innerHTML = `
                                <h3>Question ${index + 1}</h3>
                                <p>${question.question}</p>
                                <div class="quiz-options">
                                    <div class="quiz-option">
                                        <input type="radio" id="q${question.id}_a" name="q${question.id}" value="A" required>
                                        <label for="q${question.id}_a">${question.option_a}</label>
                                    </div>
                                    <div class="quiz-option">
                                        <input type="radio" id="q${question.id}_b" name="q${question.id}" value="B">
                                        <label for="q${question.id}_b">${question.option_b}</label>
                                    </div>
                                    <div class="quiz-option">
                                        <input type="radio" id="q${question.id}_c" name="q${question.id}" value="C">
                                        <label for="q${question.id}_c">${question.option_c}</label>
                                    </div>
                                    <div class="quiz-option">
                                        <input type="radio" id="q${question.id}_d" name="q${question.id}" value="D">
                                        <label for="q${question.id}_d">${question.option_d}</label>
                                    </div>
                                </div>
                            `;
                            
                            quizForm.appendChild(questionDiv);
                        });
                        
                        // Add submit button
                        const submitButton = document.createElement('button');
                        submitButton.type = 'submit';
                        submitButton.className = 'auth-btn';
                        submitButton.textContent = 'Submit Quiz';
                        quizForm.appendChild(submitButton);
                        
                        // Add form to quiz questions container
                        quizQuestions.appendChild(quizForm);
                        
                        // Handle form submission
                        quizForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            // Collect answers
                            const answers = {};
                            data.questions.forEach(question => {
                                const selectedOption = document.querySelector(`input[name="q${question.id}"]:checked`);
                                if (selectedOption) {
                                    answers[question.id] = selectedOption.value;
                                }
                            });
                            
                            // Create form data
                            const formData = new FormData();
                            formData.append('answers', JSON.stringify(answers));
                            
                            // Submit quiz
                            fetch('api/submit_quiz.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show results
                                    quizQuestions.style.display = 'none';
                                    quizResults.style.display = 'block';
                                    
                                    // Update results
                                    document.getElementById('score').textContent = data.score;
                                    document.getElementById('total').textContent = data.total;
                                    document.getElementById('points-earned').textContent = data.points_earned;
                                    
                                    // Update points in header
                                    const pointsValue = document.querySelector('.points-value');
                                    if (pointsValue) {
                                        const currentPoints = parseInt(pointsValue.textContent);
                                        pointsValue.textContent = currentPoints + data.points_earned;
                                    }
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                        });
                        
                        // Handle view leaderboard button
                        document.getElementById('view-leaderboard').addEventListener('click', function() {
                            window.location.href = 'leaderboard.php';
                        });
                    } else {
                        quizLoading.textContent = 'Error loading questions: ' + data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    quizLoading.textContent = 'An error occurred. Please try again.';
                });
        });
    </script>
</body>
</html>