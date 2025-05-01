<?php
require_once '../includes/session_config.php';

// Debug session status
debug_session();
error_log("Admin check - Starting authorization check");

// Cast admin status to boolean for strict comparison
if (isset($_SESSION['is_admin'])) {
    $_SESSION['is_admin'] = (bool)$_SESSION['is_admin'] === true;
}

// Verify session and admin status
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['is_admin'])) {
    error_log("Session not set properly - Missing required session variables");
    header("Location: ../login.php?error=session");
    exit();
}

// Strict boolean comparison for admin status
if ($_SESSION['logged_in'] !== true || $_SESSION['is_admin'] !== true) {
    error_log("Not admin or not logged in - logged_in: " . var_export($_SESSION['logged_in'], true) .
              ", is_admin: " . var_export($_SESSION['is_admin'], true));
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Not logged in or not an admin, redirect to login page
    header("Location: ../login.php?error=unauthorized");
    exit();
}
?>