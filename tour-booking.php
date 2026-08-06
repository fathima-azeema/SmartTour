<?php
// tour-booking.php
require_once 'config.php';

// Optional: Allow guest bookings (uncomment if you want to require login)
// checkLogin();

$conn = getDBConnection();

// Get tour ID from URL
$tour_id = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : 0;

// If no tour ID, redirect to search page
if ($tour_id == 0) {
    header("Location: search-tours.php?error=select_tour");
    exit();
}

// Get tour details
$sql = "SELECT * FROM tours WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search-tours.php?error=tour_not_found");
    exit();
}

$tour = $result->fetch_assoc();

// Get booking details from URL or set defaults
$tour_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d', strtotime('+7 days'));
$participants = isset($_GET['participants']) ? (int)$_GET['participants'] : 2;

// Calculate total amount
$total_amount = $tour['price_per_person'] * $participants;

// Generate booking reference
$booking_ref = 'TOUR' . date('Ymd') . rand(1000, 9999);

// Handle form submission
$booking_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $special_requests = isset($_POST['special_requests']) ? sanitize($_POST['special_requests']) : '';
    $tour_date = isset($_POST['tour_date']) ? $_POST['tour_date'] : '';
    $participants = isset($_POST['participants']) ? (int)$_POST['participants'] : 2;
    $pickup_location = isset($_POST['pickup_location']) ? sanitize($_POST['pickup_location']) : '';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'card';
    
    // Validate
    if (empty($full_name) || empty($email) || empty($phone) || empty($tour_date)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (strtotime($tour_date) < strtotime(date('Y-m-d'))) {
        $error_message = "Tour date cannot be in the past.";
    } else {
        // Save booking to database
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $total_amount = $tour['price_per_person'] * $participants;
        
        // Insert without pickup_location if column doesn't exist
        $insert_sql = "INSERT INTO tour_bookings (booking_ref, user_id, tour_id, tour_date, participants, total_amount, special_requests, payment_method, payment_status, booking_status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'confirmed')";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("siisidss", $booking_ref, $user_id, $tour_id, $tour_date, $participants, $total_amount, $special_requests, $payment_method);
        
        if ($insert_stmt->execute()) {
            $booking_success = true;
            $booking_id = $insert_stmt->insert_id;
        } else {
            $error_message = "Booking failed: " . $conn->error;
        }
        $insert_stmt->close();
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($tour['tour_name']); ?> - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #27ae60;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #e8f5e9;
            --dark: #2c3e50;
            --text: #34495e;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --success: #27ae60;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Success Message */
        .success-message {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--shadow);
            max-width: 500px;
            margin: 50px auto;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 20px;
        }

        .success-message h2 {
            color: var(--success);
            margin-bottom: 15px;
        }

        .booking-ref {
            background: var(--light);
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 20px 0;
            color: var(--secondary);
        }

        .tour-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }

        .tour-info p {
            margin: 8px 0;
            color: var(--text);
        }

        .tour-info i {
            color: var(--primary);
            width: 25px;
        }

        /* Main Booking Card */
        .booking-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Tour Preview Section */
        .tour-preview {
            background: linear-gradient(135deg, var(--primary), #1e8449);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .tour-image {
            width: 100%;
            height: 200px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .tour-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .tour-location {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .category-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .tour-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .stars {
            color: #ffd700;
        }

        .rating-text {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .highlights {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-top: auto;
        }

        .highlights h4 {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .highlights-list {
            list-style: none;
        }

        .highlights-list li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .highlights-list i {
            color: #ffd700;
        }

        /* Booking Form Section */
        .booking-form {
            padding: 40px;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: var(--secondary);
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .form-header p {
            color: #666;
        }

        /* Tour Summary */
        .tour-summary {
            background: var(--light);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: var(--text);
            font-weight: 500;
        }

        .summary-value {
            font-weight: 600;
            color: var(--secondary);
        }

        .price-highlight {
            font-size: 1.3rem;
            color: var(--primary);
            font-weight: 700;
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary);
        }

        .form-label i {
            color: var(--primary);
            margin-right: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Date Picker */
        .date-picker {
            position: relative;
        }

        .date-picker i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }

        /* Participants Selector */
        .participants-selector {
            display: flex;
            align-items: center;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .participant-btn {
            padding: 12px 20px;
            background: var(--light);
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .participant-btn:hover {
            background: var(--primary);
            color: white;
        }

        .participant-input {
            flex: 1;
            text-align: center;
            padding: 12px;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Payment Methods */
        .payment-methods {
            margin: 25px 0;
        }

        .payment-title {
            font-weight: 500;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        .payment-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--primary);
        }

        .payment-option.selected {
            border-color: var(--primary);
            background: rgba(39, 174, 96, 0.05);
        }

        .payment-option i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: var(--primary);
        }

        .payment-option span {
            display: block;
            font-size: 0.9rem;
        }

        /* Card Details */
        .card-details {
            background: var(--light);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            display: none;
        }

        .card-details.active {
            display: block;
        }

        .card-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Price Breakdown */
        .price-breakdown {
            background: var(--light);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: var(--text);
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #ddd;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--secondary);
        }

        .cancellation-policy {
            margin-top: 15px;
            font-size: 0.85rem;
            color: var(--success);
        }

        .cancellation-policy i {
            margin-right: 5px;
        }

        /* Book Button */
        .book-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), #1e8449);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(39, 174, 96, 0.3);
        }

        .book-btn i {
            margin-right: 8px;
        }

        /* Error Message */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .booking-card {
                grid-template-columns: 1fr;
            }
            
            .tour-preview {
                padding: 30px;
            }
            
            .booking-form {
                padding: 30px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .payment-options {
                grid-template-columns: 1fr;
            }
            
            .card-row {
                grid-template-columns: 1fr;
            }
            
            .tour-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($booking_success): ?>
            <!-- Success Message -->
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Tour Booked Successfully! 🎉</h2>
                <p>Your tour has been confirmed. We'll send you all the details via email.</p>
                <div class="booking-ref">
                    Booking Ref: <?php echo $booking_ref; ?>
                </div>
                
                <div class="tour-info">
                    <p><i class="fas fa-map-marked-alt"></i> <strong><?php echo htmlspecialchars($tour['tour_name']); ?></strong></p>
                    <p><i class="fas fa-calendar"></i> Date: <?php echo date('l, F j, Y', strtotime($tour_date)); ?></p>
                    <p><i class="fas fa-users"></i> Participants: <?php echo $participants; ?> people</p>
                    <p><i class="fas fa-clock"></i> Duration: <?php echo $tour['duration_days']; ?> days</p>
                    <p><i class="fas fa-map-marker-alt"></i> Location: <?php echo htmlspecialchars($tour['location']); ?></p>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="view-bookings.php" style="text-decoration: none; padding: 12px 25px; background: var(--primary); color: white; border-radius: 8px;">
                        <i class="fas fa-calendar-alt"></i> View My Bookings
                    </a>
                    <a href="search-tours.php" style="text-decoration: none; padding: 12px 25px; background: var(--light); color: var(--secondary); border-radius: 8px;">
                        <i class="fas fa-search"></i> Find Another Tour
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Booking Card -->
            <div class="booking-card">
                <!-- Left Side - Tour Preview -->
                <div class="tour-preview">
                    <div class="tour-image">
                        <img src="<?php echo htmlspecialchars($tour['image_url'] ?: 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'); ?>" 
                             alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                    </div>
                    
                    <h1 class="tour-name"><?php echo htmlspecialchars($tour['tour_name']); ?></h1>
                    
                    <div class="tour-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($tour['location']); ?></span>
                    </div>
                    
                    <span class="category-badge">
                        <i class="fas fa-tag"></i> <?php echo ucfirst($tour['category']); ?> Tour
                    </span>
                    
                    <div class="tour-meta">
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            <span><?php echo $tour['duration_days']; ?> Days</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>Max <?php echo $tour['max_participants'] ?? 20; ?></span>
                        </div>
                    </div>
                    
                    <div class="rating">
                        <div class="stars">
                            <?php 
                            $rating = $tour['rating'] ?? 4.5;
                            for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $rating): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif($i - 0.5 <= $rating): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text"><?php echo number_format($tour['rating'] ?? 4.5, 1); ?> (150+ reviews)</span>
                    </div>
                    
                    <div class="highlights">
                        <h4>Tour Highlights</h4>
                        <ul class="highlights-list">
                            <li><i class="fas fa-check-circle"></i> Expert Local Guide</li>
                            <li><i class="fas fa-check-circle"></i> All Entrance Fees</li>
                            <li><i class="fas fa-check-circle"></i> Comfortable Transport</li>
                            <li><i class="fas fa-check-circle"></i> Authentic Local Lunch</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Right Side - Booking Form -->
                <div class="booking-form">
                    <div class="form-header">
                        <h2>Book Your Tour</h2>
                        <p>Complete the details to confirm your adventure</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Tour Summary -->
                    <div class="tour-summary">
                        <div class="summary-row">
                            <span class="summary-label">Tour:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($tour['tour_name']); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Duration:</span>
                            <span class="summary-value"><?php echo $tour['duration_days']; ?> Days</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Price per person:</span>
                            <span class="summary-value price-highlight">LKR <?php echo number_format($tour['price_per_person']); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST" action="tour-booking.php?tour_id=<?php echo $tour_id; ?>" id="bookingForm">
                        <input type="hidden" name="tour_date" id="tour_date" value="<?php echo $tour_date; ?>">
                        <input type="hidden" name="participants" id="participants_input" value="<?php echo $participants; ?>">
                        
                        <!-- Date Selection -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar"></i> Select Tour Date *
                            </label>
                            <div class="date-picker">
                                <input type="date" name="tour_date_picker" id="datePicker" class="form-control" 
                                       value="<?php echo $tour_date; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        
                        <!-- Participants Selection -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-users"></i> Number of Participants *
                            </label>
                            <div class="participants-selector">
                                <button type="button" class="participant-btn" onclick="updateParticipants(-1)">-</button>
                                <input type="text" id="participants_display" class="participant-input" value="<?php echo $participants; ?>" readonly>
                                <button type="button" class="participant-btn" onclick="updateParticipants(1)">+</button>
                            </div>
                        </div>
                        
                        <!-- Pickup Location -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Pickup Location
                            </label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="Your hotel name or address">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i> Email *
                                </label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone Number *
                            </label>
                            <input type="tel" name="phone" class="form-control" placeholder="+94 77 123 4567" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-comment"></i> Special Requests
                            </label>
                            <textarea name="special_requests" class="form-control" placeholder="Dietary requirements, medical conditions, special accommodations..."></textarea>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <div class="payment-title">Select Payment Method</div>
                            <div class="payment-options">
                                <div class="payment-option selected" onclick="selectPayment('card')">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Credit Card</span>
                                </div>
                                <div class="payment-option" onclick="selectPayment('paypal')">
                                    <i class="fab fa-paypal"></i>
                                    <span>PayPal</span>
                                </div>
                                <div class="payment-option" onclick="selectPayment('bank')">
                                    <i class="fas fa-university"></i>
                                    <span>Bank Transfer</span>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="card">
                        </div>
                        
                        <!-- Card Details -->
                        <div class="card-details active" id="cardDetails">
                            <div class="form-group">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" placeholder="1234 5678 9012 3456" id="card_number">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" placeholder="John Doe">
                            </div>
                            <div class="card-row">
                                <div class="form-group">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" placeholder="123">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div class="price-breakdown">
                            <div class="price-item">
                                <span>Tour Price (<?php echo $participants; ?> person<?php echo $participants > 1 ? 's' : ''; ?>)</span>
                                <span>LKR <?php echo number_format($tour['price_per_person'] * $participants); ?></span>
                            </div>
                            <div class="price-item">
                                <span>Taxes & Fees (10%)</span>
                                <span>LKR <?php echo number_format($tour['price_per_person'] * $participants * 0.1); ?></span>
                            </div>
                            <div class="price-total">
                                <span>Total Amount</span>
                                <span>LKR <?php echo number_format($tour['price_per_person'] * $participants * 1.1); ?></span>
                            </div>
                            <div class="cancellation-policy">
                                <i class="fas fa-check-circle"></i> Free cancellation up to 7 days before tour
                            </div>
                        </div>
                        
                        <button type="submit" class="book-btn">
                            <i class="fas fa-lock"></i> Confirm Booking
                        </button>
                        
                        <p style="text-align: center; margin-top: 15px; font-size: 0.8rem; color: #999;">
                            <i class="fas fa-shield-alt"></i> Your information is secure and encrypted
                        </p>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Update participants
        function updateParticipants(change) {
            const input = document.getElementById('participants_input');
            const display = document.getElementById('participants_display');
            let value = parseInt(input.value) + change;
            
            if (value < 1) value = 1;
            if (value > <?php echo $tour['max_participants'] ?? 20; ?>) value = <?php echo $tour['max_participants'] ?? 20; ?>;
            
            input.value = value;
            display.value = value;
            
            // Update price display
            const pricePerPerson = <?php echo $tour['price_per_person']; ?>;
            const subtotal = pricePerPerson * value;
            const taxes = subtotal * 0.1;
            const total = subtotal + taxes;
            
            document.querySelector('.price-item:first-child span:last-child').textContent = 'LKR ' + subtotal.toLocaleString();
            document.querySelector('.price-item:last-child span:last-child').textContent = 'LKR ' + taxes.toLocaleString();
            document.querySelector('.price-total span:last-child').textContent = 'LKR ' + total.toLocaleString();
        }
        
        // Date picker change
        document.getElementById('datePicker')?.addEventListener('change', function() {
            document.getElementById('tour_date').value = this.value;
        });
        
        // Payment method selection
        function selectPayment(method) {
            document.getElementById('payment_method').value = method;
            
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            const cardDetails = document.getElementById('cardDetails');
            if (method === 'card') {
                cardDetails.classList.add('active');
            } else {
                cardDetails.classList.remove('active');
            }
        }
        
        // Format card number
        document.getElementById('card_number')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            e.target.value = formattedValue;
        });
        
        // Form validation
        document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
            const fullName = document.querySelector('input[name="full_name"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            const tourDate = document.getElementById('datePicker').value;
            
            if (!fullName || !email || !phone || !tourDate) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
            
            if (phone.length < 10) {
                e.preventDefault();
                alert('Please enter a valid phone number');
                return;
            }
        });
    </script>
</body>
</html>