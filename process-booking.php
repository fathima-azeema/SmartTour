<?php
// process-booking.php

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

// Get form data and sanitize inputs
$tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
$tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
$tour_price = mysqli_real_escape_string($conn, $_POST['tour_price']);
$tour_date = mysqli_real_escape_string($conn, $_POST['tour_date']);
$participants = mysqli_real_escape_string($conn, $_POST['participants']);
$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$special_requests = mysqli_real_escape_string($conn, $_POST['special_requests']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

// For card payments, get card details (in real app, these would be processed via payment gateway)
$card_number = isset($_POST['card_number']) ? mysqli_real_escape_string($conn, $_POST['card_number']) : '';
$card_name = isset($_POST['card_name']) ? mysqli_real_escape_string($conn, $_POST['card_name']) : '';
$card_expiry = isset($_POST['card_expiry']) ? mysqli_real_escape_string($conn, $_POST['card_expiry']) : '';
$card_cvv = isset($_POST['card_cvv']) ? mysqli_real_escape_string($conn, $_POST['card_cvv']) : '';

// Calculate total amount
$total_amount = $tour_price * $participants;

// Generate a unique booking reference
$booking_ref = 'TR' . date('Ymd') . strtoupper(substr(uniqid(), -6));

// Insert booking into booking_tour table
$sql = "INSERT INTO booking_tour (booking_ref, tour_id, tour_name, tour_date, participants, customer_name, customer_email, customer_phone, special_requests, payment_method, card_number, card_name, card_expiry, total_amount, booking_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'confirmed')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisssisssssssd", 
    $booking_ref, 
    $tour_id, 
    $tour_name, 
    $tour_date, 
    $participants, 
    $full_name, 
    $email, 
    $phone, 
    $special_requests, 
    $payment_method,
    $card_number,
    $card_name,
    $card_expiry,
    $total_amount
);

if ($stmt->execute()) {
    // Booking successful
    $booking_id = $stmt->insert_id;
    
    // Send confirmation email (optional)
    sendConfirmationEmail($email, $full_name, $booking_ref, $tour_name, $tour_date, $participants, $total_amount);
    
    // Redirect to confirmation page
    header("Location: booking-confirmation.php?ref=" . $booking_ref);
    exit();
} else {
    // Error handling
    error_log("Booking error: " . $stmt->error);
    header("Location: booking-error.php?error=database");
    exit();
}

$stmt->close();
$conn->close();

// Function to send confirmation email
function sendConfirmationEmail($email, $name, $booking_ref, $tour_name, $tour_date, $participants, $total_amount) {
    $subject = "Booking Confirmation - ExploreWorld Sri Lanka";
    $message = "
    <html>
    <head>
        <title>Booking Confirmation</title>
    </head>
    <body>
        <h2>Thank you for your booking, $name!</h2>
        <p>Your booking has been confirmed. Here are your booking details:</p>
        <table>
            <tr><td><strong>Booking Reference:</strong></td><td>$booking_ref</td></tr>
            <tr><td><strong>Tour:</strong></td><td>$tour_name</td></tr>
            <tr><td><strong>Date:</strong></td><td>$tour_date</td></tr>
            <tr><td><strong>Participants:</strong></td><td>$participants</td></tr>
            <tr><td><strong>Total Amount:</strong></td><td>LKR " . number_format($total_amount, 2) . "</td></tr>
        </table>
        <p>We look forward to welcoming you on your tour!</p>
        <p>Best regards,<br>ExploreWorld Sri Lanka Team</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@exploreworld.lk" . "\r\n";
    
    // In a real application, you would use a proper email library
    // mail($email, $subject, $message, $headers);
}
?>