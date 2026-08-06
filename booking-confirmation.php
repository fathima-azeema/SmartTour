<?php
// booking-confirmation.php
if (isset($_GET['ref'])) {
    $booking_ref = htmlspecialchars($_GET['ref']);
} else {
    header("Location: search-tours.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - ExploreWorld Sri Lanka</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .confirmation-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .success-icon {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .booking-ref {
            background: #ecf0f1;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0;
            display: inline-block;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">✓</div>
        <h1>Booking Confirmed!</h1>
        <p>Thank you for booking with ExploreWorld Sri Lanka. Your booking has been successfully processed.</p>
        <div class="booking-ref">Booking Reference: <?php echo $booking_ref; ?></div>
        <p>We have sent a confirmation email with all the details. Our team will contact you shortly to finalize the arrangements.</p>
        <div>
            <a href="search-tours.php" class="btn">Book Another Tour</a>
            <a href="index.php" class="btn">Return to Home</a>
        </div>
    </div>
</body>
</html>