<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Connect to database
require_once('includes/db_connect.php');

$message = '';
$error = '';

// Get current user data
$stmt = $conn->prepare("SELECT username, email, display_name, bio, profile_image, dark_mode FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $display_name = isset($_POST['display_name']) ? trim($_POST['display_name']) : '';
        $bio = trim($_POST['bio']);
        $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;

        // Validate inputs
        if (empty($username) || empty($email)) {
            $error = "Username and email are required";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if username is taken by another user
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->bind_param("si", $username, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error = "Username is already taken";
            } else {
                // Handle profile image upload
                $profile_image = $user['profile_image']; // Keep existing image by default
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
                    $target_dir = "img/profile_images/";
                    $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
                    $new_filename = uniqid('profile_') . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;

                    // Check file type
                    if ($file_extension != "jpg" && $file_extension != "jpeg" && $file_extension != "png") {
                        $error = "Only JPG, JPEG, PNG files are allowed";
                    } else if ($_FILES["profile_image"]["size"] > 5000000) { // 5MB max
                        $error = "File is too large (max 5MB)";
                    } else if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                        // Delete old profile image if it exists and is not the default
                        if ($profile_image && $profile_image != "default.jpg" && file_exists($target_dir . $profile_image)) {
                            unlink($target_dir . $profile_image);
                        }
                        $profile_image = $new_filename;
                    } else {
                        $error = "Error uploading file";
                    }
                }

                if (!$error) {
                    // Update user data
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, display_name = ?, bio = ?, profile_image = ?, dark_mode = ? WHERE id = ?");
                    $stmt->bind_param("sssssii", $username, $email, $display_name, $bio, $profile_image, $dark_mode, $_SESSION['user_id']);
                    
                    if ($stmt->execute()) {
                        $message = "Profile updated successfully";
                        // Update session variables
                        $_SESSION['username'] = $username;
                    } else {
                        $error = "Error updating profile";
                    }
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FitQuest</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/dark-theme.css">
    <link rel="stylesheet" href="css/settings.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="settings-container">
            <div class="settings-header">
                <h2>User Settings</h2>
            </div>

            <?php if ($message): ?>
                <div class="message success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="settings-form" enctype="multipart/form-data">
                <div class="profile-image-container">
                    <img class="profile-image" src="<?php echo htmlspecialchars($user['profile_image'] ? 'img/profile_images/' . $user['profile_image'] : 'https://via.placeholder.com/150'); ?>" alt="Profile picture" id="profile-preview">
                    <div class="form-group">
                        <label for="profile_image">Change Profile Picture</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png" onchange="previewImage(this);">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                
                

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                </div>

                

                <button type="submit" name="update_profile" class="submit-btn">Save Changes</button>
            </form>
        </div>
    </main>
<script src="js/header.js"></script>
<script>
    // Preview profile image before upload
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
    </script>
</body>
</html>