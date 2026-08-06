<?php
// Include database connection
include('db_connection.php');

// Get the booking_id from the URL
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Fetch booking details from the database based on booking_id
$query = "SELECT * FROM bookings WHERE booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// Check if the booking data is fetched
if (!$booking) {
    echo "Booking not found!<br>"; 
    exit;
}

// Fetch hotel details based on hotel_id from the booking
$query = "SELECT * FROM hotels WHERE hotel_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $booking['hotel_id']);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();

// Check if the hotel data is fetched
if (!$hotel) {
    echo "Hotel not found!<br>"; 
    exit;
}

// Calculate the total price (for validation purpose)
$total_price = $booking['num_rooms'] * $booking['num_days'] * $hotel['price']; // Validate if the calculation matches the stored price
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="css/payment-confirmation.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>

<!-- Header Section -->
<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="search-hotels.php">Search Hotels</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>
</header>

<!-- Payment Confirmation Section -->
<section class="confirmation-section">
    <h2>Payment Confirmation</h2>

    <!-- Booking Details -->
    <div class="booking-details">
        <h3>Booking Details</h3>
        <p><strong>Hotel Name:</strong> <?php echo $hotel['hotel_name']; ?></p>
        <p><strong>Location:</strong> <?php echo $hotel['location']; ?></p>
        <p><strong>Number of Rooms:</strong> <?php echo $booking['num_rooms']; ?></p>
        <p><strong>Number of Days:</strong> <?php echo $booking['num_days']; ?></p>
        <p><strong>Total Price:</strong> $<?php echo $total_price; ?></p>
    </div>

    <!-- Payment Form -->
    <div class="payment-form">
        <h3>Enter Your Payment Details</h3>
        <form action="process-payment.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
            <input type="hidden" name="hotel_id" value="<?php echo $hotel['hotel_id']; ?>">
            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

            <label for="cardholder_name">Cardholder Name:</label>
            <input type="text" id="cardholder_name" name="cardholder_name" required placeholder="Enter your cardholder name">

            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" required placeholder="Enter your card number" pattern="\d{16}" maxlength="16" oninput="this.value=this.value.replace(/[^0-9]/g,'')">

            <label for="expiration_date">Expiration Date:</label>
            <input type="month" id="expiration_date" name="expiration_date" required>

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" required placeholder="Enter CVV" pattern="\d{3}" maxlength="3">

            <button type="submit" class="btn">Submit Payment</button>
        </form>
    </div>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 SmartTour. All Rights Reserved.</p>
</footer>

</body>
</html>
