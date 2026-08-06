<?php
// Include database connection
include('db_connection.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get payment details from the form
    $booking_id = $_POST['booking_id'];
    $card_number = $_POST['card_number'];
    $expiration_date = $_POST['expiration_date'];
    $cvv = $_POST['cvv'];
    $cardholder_name = $_POST['cardholder_name'];
    $total_price = $_POST['total_price'];

    // Insert payment details into the payments table
    $query = "INSERT INTO payments (booking_id, card_number, expiration_date, cvv, cardholder_name, total_price, payment_status) 
              VALUES (?, ?, ?, ?, ?, ?, 'completed')"; // Assume payment status is 'completed' here

    $stmt = $conn->prepare($query);
    $stmt->bind_param('issssi', $booking_id, $card_number, $expiration_date, $cvv, $cardholder_name, $total_price);
    
    if ($stmt->execute()) {
        // After successful payment, redirect to payment confirmation page
        header("Location: payment-confirmation.php?booking_id=" . $booking_id);
        exit();
    } else {
        // Handle errors if any
        echo "Error processing payment. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="css/payment-confirmation.css">
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

    <!-- Payment Details -->
    <div class="payment-details">
        <h3>Payment Details</h3>
        <p><strong>Cardholder Name:</strong> <?php echo $payment['cardholder_name']; ?></p>
        <p><strong>Card Number:</strong> <?php echo "**** **** **** " . substr($payment['card_number'], -4); ?></p>
        <p><strong>Expiration Date:</strong> <?php echo $payment['expiration_date']; ?></p>
        <p><strong>Payment Status:</strong> <?php echo ucfirst($payment['payment_status']); ?></p>
        <p><strong>Total Paid:</strong> $<?php echo $payment['total_price']; ?></p>
    </div>

    <!-- Confirmation Message -->
    <div class="confirmation-message">
        <h3>Your Booking is Confirmed!</h3>
        <p>Thank you for booking with us. You will receive a confirmation email shortly.</p>
        <p><a href="index.php" class="btn">Go to Homepage</a></p>
    </div>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 SmartTour. All Rights Reserved.</p>
</footer>

</body>
</html>
