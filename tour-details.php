<?php
// tour-details.php

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

// Get tour ID from URL parameter
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    
    // Prepare and execute query
    // First, let's check what columns exist in your tours table
    $sql = "SELECT * FROM tours WHERE tour_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $tour_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tour = $result->fetch_assoc();
        } else {
            // Tour not found, redirect to tours page
            header("Location: search-tours.php");
            exit();
        }
        $stmt->close();
    } else {
        // If there's an error with the query, show the error and check table structure
        die("Error in query: " . $conn->error);
    }
} else {
    // No ID provided, redirect to tours page
    header("Location: search-tours.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tour['tour_name'] ?? 'Tour Details'); ?> - ExploreWorld Sri Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add your CSS styles here */
        /* ... (use the same CSS as in the previous tour-details page) ... */
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">Explore<span>World</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="search-hotels.php">Hotels</a></li>
                    <li><a href="search-restaurants.php">Restaurants</a></li>
                    <li><a href="search-tours.php">Tours</a></li>
                    <li><a href="apply-for-jobs.php">Careers</a></li>
                    <li><a href="about.php">About</a></li>
                </ul>
                <div class="auth-buttons">
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn btn-accent">Sign Up</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Tour Details Content -->
    <section class="tour-details">
        <div class="container">
            <div class="details-container">
                <div class="tour-content">
                    <!-- Display tour details dynamically -->
                    <h1><?php echo htmlspecialchars($tour['tour_name']); ?></h1>
                    <p><?php echo htmlspecialchars($tour['description'] ?? 'No description available.'); ?></p>
                    <!-- Add more dynamic content as needed -->
                </div>
                
                <div class="booking-form-container">
                    <!-- Booking form remains the same -->
                </div>
            </div>
        </div>
    </section>
</body>
</html>