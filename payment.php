<?php
// Include database connection
include('db_connection.php');

// Get the booking details from the URL
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Fetch booking details from the database based on booking_id
$query = "SELECT * FROM bookings WHERE booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// Check if booking is found
if (!$booking) {
    echo "Booking not found!";
    exit;
}

// Fetch hotel details from the database
$query = "SELECT * FROM hotels WHERE hotel_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $booking['hotel_id']);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();

// Calculate the total price
$total_price = $hotel['price'] * $booking['num_rooms'] * $booking['num_days'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - <?php echo $hotel['hotel_name']; ?></title>
    <link rel="stylesheet" href="css/payment.css">
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

<!-- Payment Details Section -->
<section class="payment-section">
    <h2>Payment for Booking: <?php echo $hotel['hotel_name']; ?></h2>
    <p><strong>Location:</strong> <?php echo $hotel['location']; ?></p>
    <p><strong>Price per Night:</strong> $<?php echo $hotel['price']; ?></p>
    <p><strong>Number of Rooms:</strong> <?php echo $booking['num_rooms']; ?></p>
    <p><strong>Number of Days:</strong> <?php echo $booking['num_days']; ?></p>
    <p><strong>Total Price:</strong> $<?php echo $total_price; ?></p>

    <h3>Enter Payment Details</h3>
    <!-- Payment Form -->
    <form action="process-payment.php" method="POST">
        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

        <label for="card_number">Card Number:</label>
        <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" required>

        <label for="expiration_date">Expiration Date:</label>
        <input type="month" name="expiration_date" id="expiration_date" required>

        <label for="cvv">CVV:</label>
        <input type="text" name="cvv" id="cvv" placeholder="123" required>

        <label for="cardholder_name">Cardholder Name:</label>
        <input type="text" name="cardholder_name" id="cardholder_name" placeholder="John Doe" required>

        <button type="submit" class="btn">Submit Payment</button>
    </form>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 SmartTour. All Rights Reserved.</p>
</footer>

</body>
</html>
