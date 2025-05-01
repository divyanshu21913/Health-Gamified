<?php
require_once '../includes/session_config.php';
require_once 'admin_check.php';
require_once '../includes/db_connect.php';

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php?msg=user_deleted");
    exit();
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?");
    $stmt->bind_param("ssii", $username, $email, $is_admin, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php?msg=user_updated");
    exit();
}

// Get search term if any
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_term = "%$search%";

// Get users with search
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $search_term, $search_term);
} else {
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY id DESC");
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FitQuest Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .users-table th, .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .users-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .search-box {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .search-box input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
        .search-box button {
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            color: white;
        }
        .edit-btn {
            background: #28a745;
        }
        .delete-btn {
            background: #dc3545;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            position: relative;
            background: white;
            margin: 15% auto;
            padding: 20px;
            width: 50%;
            border-radius: 5px;
        }
        .close {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <span class="material-symbols-outlined">fitness_center</span>
            <h1>FitQuest Admin</h1>
        </div>
        <nav class="desktop-nav">
            <ul>
                <li><a href="dashboard.php">Admin Dashboard</a></li>
                <li><a href="../challenges.php">Challenges</a></li>
                <li><a href="../leaderboard.php">Leaderboard</a></li>
                <li><a href="../quiz.php">Health Quiz</a></li>
                <li><a href="contact_messages.php">Messages</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <div class="user-avatar" id="user-menu-btn">
                <img src="https://via.placeholder.com/40" alt="Admin avatar">
            </div>
            <div class="user-dropdown" id="user-dropdown">
                <ul>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="../api/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main>
        <section class="page-header">
            <h2>Manage Users</h2>
        </section>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php 
                    switch($_GET['msg']) {
                        case 'user_deleted':
                            echo "User successfully deleted";
                            break;
                        case 'user_updated':
                            echo "User information updated successfully";
                            break;
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="search-box">
            <form action="" method="GET">
                <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Admin Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                    <td>
                        <button class="action-btn edit-btn" onclick="openEditModal(<?php 
                            echo htmlspecialchars(json_encode([
                                'id' => $user['id'],
                                'username' => $user['username'],
                                'email' => $user['email'],
                                'is_admin' => $user['is_admin']
                            ])); 
                        ?>)">Edit</button>
                        <button class="action-btn delete-btn" onclick="confirmDelete(<?php echo $user['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Edit User Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit User</h3>
                <form id="editUserForm" method="POST">
                    <input type="hidden" name="update_user" value="1">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="edit_username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_admin" id="edit_is_admin">
                            Admin Status
                        </label>
                    </div>

                    <button type="submit" class="action-btn edit-btn">Update User</button>
                </form>
            </div>
        </div>

        <!-- Delete User Form (Hidden) -->
        <form id="deleteUserForm" method="POST" style="display: none;">
            <input type="hidden" name="delete_user" value="1">
            <input type="hidden" name="user_id" id="delete_user_id">
        </form>
    </main>

    <script>
        // User menu dropdown
        document.getElementById('user-menu-btn').addEventListener('click', function() {
            document.getElementById('user-dropdown').classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.remove('active');
            }
        });

        // Edit modal functions
        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_is_admin').checked = user.is_admin === 1;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Delete user function
        function confirmDelete(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                document.getElementById('delete_user_id').value = userId;
                document.getElementById('deleteUserForm').submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>