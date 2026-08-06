<?php
// config.php - Database configuration and essential functions

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tourism_db');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Sanitize input
function sanitize($input) {
    $conn = getDBConnection();
    $sanitized = mysqli_real_escape_string($conn, $input);
    $conn->close();
    return htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current user type
function getCurrentUserType() {
    return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getDBConnection();
    $user_id = getCurrentUserId();
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    }
    
    $stmt->close();
    $conn->close();
    return null;
}

// Close database connection
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>