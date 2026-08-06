<?php
// register.php

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourism_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $password = $_POST['password'];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
    
    // Check if email already exists
    $check_email = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        die("Email already registered. Please use another email or <a href='login.php'>login</a>.");
    }
    $stmt->close();
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user into database
    $sql = "INSERT INTO users (first_name, last_name, email, phone, user_type, password) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $user_type, $hashed_password);
    
    if ($stmt->execute()) {
        // Registration successful
        $user_id = $stmt->insert_id;
        
        // Start session
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['first_name'] = $first_name;
        
        // Redirect to appropriate dashboard based on user type
        switch($user_type) {
            case 'tourist':
                header("Location: tourist-dashboard.php");
                break;
            case 'student':
                header("Location: student-dashboard.php");
                break;
            case 'guide':
                header("Location: guide-dashboard.php");
                break;
            default:
                header("Location: dashboard.php");
        }
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>