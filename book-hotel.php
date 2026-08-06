<?php
// book-hotel.php
include('db_connection.php');

// Get the hotel_id from the URL
$hotel_id = isset($_GET['hotel_id']) ? $_GET['hotel_id'] : 0;

// Debugging: Check the hotel_id
echo "Hotel ID: " . $hotel_id . "<br>"; // Debugging line

// Fetch hotel details from the database based on hotel_id
$query = "SELECT * FROM hotels WHERE hotel_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();

// Debugging: Check if hotel data is fetched
if (!$hotel) {
    echo "Hotel not found!<br>";
    exit;
}

echo "Hotel found: " . $hotel['hotel_name'] . "<br>"; // Debugging line to check hotel name
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotel - <?php echo $hotel['hotel_name']; ?></title>
<link rel="stylesheet" href="css/booking.css">
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
                <a href="index.php" class="nav-link active">Home</a>
                <a href="about-us.php" class="nav-link">About</a>
                <a href="services.php" class="nav-link">Services</a>
                <a href="contact.php" class="nav-link">Contact</a>
            </nav>

            <!-- User Actions (Login and Sign Up) -->
            <div class="header-actions">
                <button class="btn btn-secondary">Login</button>
                <button class="btn btn-primary" onclick="window.location.href='signup.html'">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<!-- Hotel Details Section -->
<section class="hotel-details">
    <h1>Booking: <?php echo $hotel['hotel_name']; ?></h1>
    <p><strong>Location:</strong> <?php echo $hotel['location']; ?></p>
    <p><strong>Price per Night:</strong> $<?php echo $hotel['price']; ?></p>
    <p><strong>Amenities:</strong> <?php echo $hotel['amenities']; ?></p>
    <p><strong>Rating:</strong> <?php echo $hotel['rating']; ?> <i class="fas fa-star"></i></p>

    <h2>Booking Form</h2>
    <!-- Booking Form -->
    <form action="payment-confirmation.php" method="POST">
        <input type="hidden" name="hotel_id" value="<?php echo $hotel['hotel_id']; ?>">

        <label for="num_rooms">Number of Rooms:</label>
        <input type="number" name="num_rooms" id="num_rooms" required>

        <label for="num_days">Number of Days:</label>
        <input type="number" name="num_days" id="num_days" required>

        <label for="check_in">Check-in Date:</label>
        <input type="date" name="check_in" id="check_in" required>

        <label for="check_out">Check-out Date:</label>
        <input type="date" name="check_out" id="check_out" required>

        <button type="submit" class="btn">Proceed to Payment</button>
    </form>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 SmartTour. All Rights Reserved.</p>
</footer>


</body>
</html>
