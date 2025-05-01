<?php
// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 3600, // 1 hour
    'path' => '/',     // Available across all paths
    'domain' => '',    // Current domain only
    'secure' => false, // Since we're using localhost
    'httponly' => true // Protect against XSS
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug function for session issues
function debug_session() {
    error_log("Session ID: " . session_id());
    error_log("Session Data: " . print_r($_SESSION, true));
    error_log("Cookie Data: " . print_r($_COOKIE, true));
}