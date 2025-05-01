<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Initialize variables
$success_message = "";
$error_message = "";
$name = $email = $subject = $message = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FitQuest</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Contact Form Specific Styles */
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-xl);
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xl);
        }
        
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }
        
        .contact-form {
            background-color: var(--background-color);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: var(--spacing-xl);
        }
        
        .contact-info {
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .contact-info h3 {
            font-size: 1.5rem;
            margin-bottom: var(--spacing-md);
        }
        
        .contact-info p {
            margin-bottom: var(--spacing-lg);
            line-height: 1.6;
        }
        
        .contact-methods {
            margin-top: var(--spacing-xl);
        }
        
        .contact-method {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-md);
        }
        
        .contact-method .icon {
            margin-right: var(--spacing-md);
            font-size: 1.5rem;
        }
        
        .social-links {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-xl);
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .social-link:hover {
            background-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-3px);
        }
        
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        
        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: var(--spacing-sm);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }
        
        .form-group.error input,
        .form-group.error textarea {
            border-color: var(--danger-color);
        }
        
        .error-text {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: var(--spacing-xs);
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .form-group.error .error-text {
            display: block;
            opacity: 1;
        }
        
        .form-group.error input,
        .form-group.error textarea {
            border-color: var(--danger-color);
            background-color: rgba(255, 0, 0, 0.02);
        }
        
        .auth-message {
            max-width: 600px;
            margin: 1rem auto;
            padding: 1rem;
            border-radius: var(--border-radius);
            text-align: center;
        }
        
        .auth-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 1.5rem;
            font-size: 1.1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .auth-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .contact-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: var(--spacing-sm) var(--spacing-lg);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .contact-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .contact-map {
            margin-top: var(--spacing-xl);
            border-radius: var(--border-radius);
            overflow: hidden;
            height: 200px;
        }
        
        .contact-map iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <?php
    // Process form submission after header (to maintain DB connection)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
        error_log("Contact form submitted with data: " . json_encode($_POST));
        try {
            // Ensure the contact_messages table exists
            require_once('admin/setup_contact_messages.php');
            error_log("Contact messages table setup completed");
            
            // Get form data and sanitize inputs
            $name = htmlspecialchars(trim($_POST['name']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $subject = htmlspecialchars(trim($_POST['subject']));
            $message = htmlspecialchars(trim($_POST['message']));
            
            // Validate inputs
            $errors = [];
            
            if (empty($name)) {
                $errors[] = "Name is required";
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Valid email is required";
            }
            
            if (empty($subject)) {
                $errors[] = "Subject is required";
            }
            
            if (empty($message)) {
                $errors[] = "Message is required";
            }
            
            // If no errors, insert into database
            if (empty($errors)) {
                try {
                    // Prepare statement with error handling
                    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                    if (!$stmt) {
                        error_log("Failed to prepare statement: " . $conn->error);
                        throw new Exception("Database error: Could not prepare statement");
                    }

                    // Bind parameters with error handling
                    if (!$stmt->bind_param("ssss", $name, $email, $subject, $message)) {
                        error_log("Failed to bind parameters: " . $stmt->error);
                        throw new Exception("Database error: Could not bind parameters");
                    }

                    // Execute with error handling
                    if (!$stmt->execute()) {
                        error_log("Failed to execute statement: " . $stmt->error);
                        throw new Exception("Database error: Could not save message");
                    }

                    error_log("Message inserted successfully, rows affected: " . $stmt->affected_rows);
                    $success_message = "Message sent successfully! 🎉 Our team will review your message and get back to you within 24-48 hours. Thank you for reaching out to us!";
                    
                    // Clear form data after successful submission
                    $name = $email = $subject = $message = "";
                    $stmt->close();
                } catch (Exception $e) {
                    $error_message = "Sorry, we couldn't send your message. Please try again later or contact support directly.";
                    error_log("Contact form database error: " . $e->getMessage());
                }
            } else {
                $error_message = implode("<br>", $errors);
            }
            
        } catch (Exception $e) {
            $error_message = "Sorry, we couldn't process your request. Please try again later.";
            error_log("Contact form outer error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }
    ?>

    <main>
        <section class="page-header">
            <h2>Contact Us</h2>
            <p>Have questions or feedback? We'd love to hear from you!</p>
        </section>

        <?php if (!empty($success_message)): ?>
            <div class="auth-message success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="auth-message error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="contact-container">
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form id="contact-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group" id="name-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                        <span class="error-text">Please enter your name</span>
                    </div>
                    
                    <div class="form-group" id="email-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                        <span class="error-text">Please enter a valid email address</span>
                    </div>
                    
                    <div class="form-group" id="subject-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" value="<?php echo isset($subject) ? $subject : ''; ?>" required>
                        <span class="error-text">Please enter a subject</span>
                    </div>
                    
                    <div class="form-group" id="message-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" required><?php echo isset($message) ? $message : ''; ?></textarea>
                        <span class="error-text">Please enter your message</span>
                    </div>
                    
                    <button type="submit" name="submit_contact" class="contact-btn">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info">
                <div>
                    <h3>Get in Touch</h3>
                    <p>Have questions about our health gamification platform? Want to provide feedback or report an issue? We're here to help! Fill out the form or contact us directly using the information below.</p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <span class="material-symbols-outlined icon">location_on</span>
                            <span>Lovely Professional University</span>
                        </div>
                        
                        <div class="contact-method">
                            <span class="material-symbols-outlined icon">call</span>
                            <span>+91 8595116671</span>
                        </div>
                        
                        <div class="contact-method">
                            <span class="material-symbols-outlined icon">mail</span>
                            <span>contact@fitquest.com</span>
                        </div>
                        
                        <div class="contact-method">
                            <span class="material-symbols-outlined icon">schedule</span>
                            <span>Monday - Friday: 9am - 5pm</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                        <a href="#" class="social-link">
                            <span class="material-symbols-outlined">alternate_email</span>
                        </a>
                        <a href="#" class="social-link">
                            <span class="material-symbols-outlined">forum</span>
                        </a>
                        <a href="#" class="social-link">
                            <span class="material-symbols-outlined">share</span>
                        </a>
                    </div>
                    
                    <div class="contact-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3410.748074314871!2d75.70229287505849!3d31.2553966601867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391a5f5e9c489cf3%3A0x4049a5409d53c300!2sLovely%20Professional%20University!5e0!3m2!1sen!2sin!4v1744729859995!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                    </div>
                </div>
            </div>
        </div>
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
        <a href="activity_tracker.php">
            <span class="material-symbols-outlined">monitor_heart</span>
            <span>Activity</span>
        </a>
    </nav>
<script src="js/header.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success message if it exists
        const successMessage = document.querySelector('.auth-message.success');
        if (successMessage) {
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Form validation
        const contactForm = document.getElementById('contact-form');
        
        if (contactForm) {
            // Add hidden submit_contact field on page load
            const submitInput = document.createElement('input');
            submitInput.type = 'hidden';
            submitInput.name = 'submit_contact';
            submitInput.value = '1';
            contactForm.appendChild(submitInput);

            contactForm.addEventListener('submit', function(e) {
                let isValid = true;
                const formData = new FormData(this);
                
                document.querySelectorAll('.form-group').forEach(group => {
                    group.classList.remove('error');
                });

                const fields = [
                    { id: 'name', validate: value => value.trim() !== '' },
                    { id: 'email', validate: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim()) },
                    { id: 'subject', validate: value => value.trim() !== '' },
                    { id: 'message', validate: value => value.trim() !== '' }
                ];

                fields.forEach(field => {
                    const value = formData.get(field.id);
                    if (!field.validate(value)) {
                        document.getElementById(`${field.id}-group`).classList.add('error');
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    const firstError = document.querySelector('.form-group.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });

            let debounceTimeout;
            const validateField = (input) => {
                const value = input.value.trim();
                const formGroup = input.closest('.form-group');
                const isEmail = input.id === 'email';
                const isValid = isEmail ?
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) :
                    value !== '';
                formGroup.classList.toggle('error', !isValid);
            };

            contactForm.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('input', () => {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => validateField(input), 300);
                });
                input.addEventListener('blur', () => validateField(input));
            });
        }
    });
</script>
</body>
</html>