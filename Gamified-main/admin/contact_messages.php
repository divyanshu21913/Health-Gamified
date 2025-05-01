<?php
// Start session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Connect to database
require_once('../includes/db_connect.php');

// Handle message status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $message_id = $_POST['message_id'];
    $new_status = $_POST['new_status'];
    
    $update_stmt = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $message_id);
    $update_stmt->execute();
    $update_stmt->close();
}

// Handle message deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    
    $delete_stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $delete_stmt->bind_param("i", $message_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

// Get messages with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total count
$count_result = $conn->query("SELECT COUNT(*) as total FROM contact_messages");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Get messages for current page
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($status_filter === 'all') {
    $query = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
} else {
    $query = "SELECT * FROM contact_messages WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $status_filter, $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Panel</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Admin Panel Specific Styles */
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xl);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .admin-filters {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        
        .filter-btn {
            padding: var(--spacing-xs) var(--spacing-sm);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--background-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .filter-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .messages-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--background-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        
        .messages-table th,
        .messages-table td {
            padding: var(--spacing-sm);
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .messages-table th {
            background-color: var(--background-light);
            font-weight: 600;
        }
        
        .messages-table tr:last-child td {
            border-bottom: none;
        }
        
        .messages-table tr:hover {
            background-color: var(--background-light);
        }
        
        .message-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .message-status.unread {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }
        
        .message-status.read {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .message-status.replied {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .action-btn {
            padding: 4px 8px;
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--background-light);
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 4px;
        }
        
        .action-btn:hover {
            background-color: var(--border-color);
        }
        
        .action-btn.view {
            color: var(--primary-color);
        }
        
        .action-btn.delete {
            color: var(--danger-color);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: var(--spacing-lg);
            gap: var(--spacing-xs);
        }
        
        .pagination a,
        .pagination span {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-sm);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--text-color);
            min-width: 32px;
            text-align: center;
        }
        
        .pagination a:hover {
            background-color: var(--background-light);
        }
        
        .pagination .current {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        /* Message Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
        }
        
        .modal-content {
            background-color: var(--background-color);
            margin: 10% auto;
            padding: var(--spacing-xl);
            border-radius: var(--border-radius);
            max-width: 600px;
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            top: var(--spacing-md);
            right: var(--spacing-md);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .message-header {
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }
        
        .message-meta {
            display: flex;
            justify-content: space-between;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: var(--spacing-xs);
        }
        
        .message-subject {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
        }
        
        .message-sender {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }
        
        .message-body {
            line-height: 1.6;
            margin-bottom: var(--spacing-lg);
        }
        
        .message-actions {
            display: flex;
            gap: var(--spacing-sm);
        }
        
        .modal-btn {
            padding: var(--spacing-xs) var(--spacing-md);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
        }
        
        .modal-btn.primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .modal-btn.secondary {
            background-color: var(--background-light);
            color: var(--text-color);
        }
        
        .modal-btn.danger {
            background-color: var(--danger-color);
            color: white;
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="users.php">Users</a></li>
                <li class="active"><a href="contact_messages.php">Contact Messages</a></li>
                <li><a href="challenges.php">Challenges</a></li>
                <li><a href="quiz.php">Quiz Management</a></li>
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
        <div class="admin-container">
            <div class="admin-header">
                <h2>Contact Messages</h2>
                <a href="../index.php" class="action-btn">Back to Site</a>
            </div>
            
            <div class="admin-filters">
                <a href="?status=all" class="filter-btn <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All Messages</a>
                <a href="?status=unread" class="filter-btn <?php echo $status_filter === 'unread' ? 'active' : ''; ?>">Unread</a>
                <a href="?status=read" class="filter-btn <?php echo $status_filter === 'read' ? 'active' : ''; ?>">Read</a>
                <a href="?status=replied" class="filter-btn <?php echo $status_filter === 'replied' ? 'active' : ''; ?>">Replied</a>
            </div>
            
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <p>No messages found.</p>
                </div>
            <?php else: ?>
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <span class="message-status <?php echo $message['status']; ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn view" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    <button class="action-btn delete" onclick="confirmDelete(<?php echo $message['id']; ?>)">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>">&laquo;</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>">&raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Message View Modal -->
    <div id="message-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            
            <div id="message-details">
                <!-- Message details will be loaded here -->
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
            
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this message? This action cannot be undone.</p>
            
            <div class="message-actions">
                <button class="modal-btn secondary" onclick="closeDeleteModal()">Cancel</button>
                <form method="POST" id="delete-form">
                    <input type="hidden" name="message_id" id="delete-message-id">
                    <input type="hidden" name="delete_message" value="1">
                    <button type="submit" class="modal-btn danger">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Setup user dropdown
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
        
        // View message details
        function viewMessage(messageId) {
            // Fetch message details via AJAX
            fetch(`get_message.php?id=${messageId}`, {
                headers: {
                    'Cache-Control': 'no-cache'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const message = data.message;
                        
                        // Update message status to "read" if it was "unread"
                        if (message.status === 'unread') {
                            const formData = new FormData();
                            formData.append('message_id', messageId);
                            formData.append('new_status', 'read');
                            formData.append('update_status', '1');
                            
                            fetch('contact_messages.php', {
                                method: 'POST',
                                body: formData
                            }).then(() => {
                                // Refresh status display
                                const statusElements = document.querySelectorAll('.message-status');
                                statusElements.forEach(el => {
                                    if (el.closest('tr').querySelector('button').getAttribute('onclick').includes(messageId)) {
                                        el.textContent = 'Read';
                                        el.className = 'message-status read';
                                    }
                                });
                            });
                        }
                        
                        // Populate modal with message details
                        document.getElementById('message-details').innerHTML = `
                            <div class="message-header">
                                <div class="message-meta">
                                    <span>${new Date(message.created_at).toLocaleString()}</span>
                                    <span class="message-status ${message.status}">${message.status.charAt(0).toUpperCase() + message.status.slice(1)}</span>
                                </div>
                                <div class="message-subject">${message.subject}</div>
                                <div class="message-sender">
                                    <span>From: ${message.name} (${message.email})</span>
                                </div>
                            </div>
                            <div class="message-body">
                                ${message.message.replace(/\n/g, '<br>')}
                            </div>
                            <div class="message-actions">
                                <form method="POST">
                                    <input type="hidden" name="message_id" value="${message.id}">
                                    <input type="hidden" name="new_status" value="replied">
                                    <input type="hidden" name="update_status" value="1">
                                    <button type="submit" class="modal-btn primary">Mark as Replied</button>
                                </form>
                                <button class="modal-btn secondary" onclick="closeModal()">Close</button>
                            </div>
                        `;
                        
                        // Show modal
                        document.getElementById('message-modal').style.display = 'block';
                    } else {
                        alert('Error loading message details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading message details.');
                });
        }
        
        // Close message modal
        function closeModal() {
            document.getElementById('message-modal').style.display = 'none';
        }
        
        // Confirm delete
        function confirmDelete(messageId) {
            document.getElementById('delete-message-id').value = messageId;
            document.getElementById('delete-modal').style.display = 'block';
        }
        
        // Close delete modal
        function closeDeleteModal() {
            document.getElementById('delete-modal').style.display = 'none';
        }
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const messageModal = document.getElementById('message-modal');
            const deleteModal = document.getElementById('delete-modal');
            
            if (event.target === messageModal) {
                closeModal();
            }
            
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>