<?php
// session-check.php - Session verification and protection

// Include configuration
require_once 'config.php';

// Function to check if user is logged in
function checkLogin() {
    if (!isLoggedIn()) {
        // Store the current page for redirect after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to login page
        redirect('login.php');
    }
}

// Function to check specific user type
function requireUserType($required_type) {
    checkLogin(); // First check if user is logged in
    
    $current_type = getCurrentUserType();
    
    if ($current_type != $required_type) {
        // User doesn't have required type
        $_SESSION['error'] = "You don't have permission to access this page.";
        redirect('dashboard.php');
    }
}

// Function to check multiple user types
function requireUserTypes($allowed_types) {
    checkLogin(); // First check if user is logged in
    
    $current_type = getCurrentUserType();
    
    if (!in_array($current_type, $allowed_types)) {
        // User type not allowed
        $_SESSION['error'] = "Access denied. You don't have permission to access this page.";
        redirect('dashboard.php');
    }
}

// Function to prevent logged-in users from accessing login/signup pages
function redirectIfLoggedIn($redirect_to = 'dashboard.php') {
    if (isLoggedIn()) {
        redirect($redirect_to);
    }
}

// Function to check CSRF token
function checkCSRFToken() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to validate user session
function validateSession() {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Additional security checks
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Check if session fingerprint matches
    $session_fingerprint = md5($user_agent . $ip_address);
    
    if (!isset($_SESSION['fingerprint'])) {
        $_SESSION['fingerprint'] = $session_fingerprint;
        return true;
    }
    
    if ($_SESSION['fingerprint'] !== $session_fingerprint) {
        // Session hijacking detected
        session_destroy();
        return false;
    }
    
    // Check session age (max 2 hours)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
        session_destroy();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    return true;
}

// Function to regenerate session ID
function regenerateSession() {
    if (isLoggedIn()) {
        session_regenerate_id(true);
        $_SESSION['new_session_id'] = session_id();
    }
}

// Auto-validate session on every page load
function autoSessionCheck() {
    if (!validateSession()) {
        session_destroy();
        if (basename($_SERVER['PHP_SELF']) != 'login.php') {
            redirect('login.php?session=expired');
        }
    }
    
    // Regenerate session ID every 5 minutes for security
    if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration'] > 300)) {
        regenerateSession();
        $_SESSION['last_regeneration'] = time();
    }
}

// Call auto session check
autoSessionCheck();
?>