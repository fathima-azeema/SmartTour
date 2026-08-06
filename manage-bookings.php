<?php
// Include database connection
include('db_connection.php');

// Simulate logged-in guide_id for testing purposes
$guide_id = 1;  // Example guide_id

// Fetch bookings for the guide, including tour details
$query = "
    SELECT b.booking_id, b.num_rooms, b.num_days, b.check_in, b.check_out, b.total_price, t.tour_name
    FROM bookings b
    JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.guide_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $guide_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if any bookings exist for this guide
if ($result->num_rows > 0) {
    // Debugging: Output the number of rows fetched
    echo "Found " . $result->num_rows . " bookings.<br>"; // Debugging line
} else {
    // Debugging: Output a message if no bookings are found
    echo "No bookings found for this guide.<br>"; // Debugging line
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="css/manage-bookings.css">
<link rel="stylesheet" href="css/header.css">
</head>
<body>

<!-- Header Section -->
<header id="header" class="header sticky">
    <div class="container">
        <div class="header-content">
            <!-- Logo Section -->
            <a href="index.html" class="logo">
                <i data-lucide="map-pin" class="logo-icon"></i>
                <span class="logo-text"> ✈︎⁀𓍙SmartTour</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="nav-desktop">
                <a href="index.html" class="nav-link active">Home</a>
                <a href="about-us.php" class="nav-link">About</a>
                <a href="services.html" class="nav-link">Services</a>
                <a href="contact.html" class="nav-link">Contact</a>
            </nav>

            <!-- User Actions (Login and Sign Up) -->
            <div class="header-actions">
                <button class="btn btn-secondary">Login</button>
                <button class="btn btn-primary" onclick="window.location.href='signup.html'">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<!-- Manage Bookings Section -->
<section class="manage-bookings">
    <h2>Manage Your Tour Bookings</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($booking = $result->fetch_assoc()) {
            echo '<div class="booking-item">';
            echo '<h4>Tour Name: ' . $booking['tour_name'] . '</h4>';
            echo '<p><strong>Booking ID:</strong> ' . $booking['booking_id'] . '</p>';
            echo '<p><strong>Check-in Date:</strong> ' . $booking['check_in'] . '</p>';
            echo '<p><strong>Check-out Date:</strong> ' . $booking['check_out'] . '</p>';
            echo '<p><strong>Number of Rooms:</strong> ' . $booking['num_rooms'] . '</p>';
            echo '<p><strong>Number of Days:</strong> ' . $booking['num_days'] . '</p>';
            echo '<p><strong>Total Price:</strong> $' . $booking['total_price'] . '</p>';
            echo '<a href="cancel-booking.php?booking_id=' . $booking['booking_id'] . '" class="btn">Cancel Booking</a>';
            echo '</div>';
        }
    } else {
        echo "<p>No bookings found for this guide.</p>";
    }
    ?>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 SmartTour. All Rights Reserved.</p>
</footer>

</body>
</html>
