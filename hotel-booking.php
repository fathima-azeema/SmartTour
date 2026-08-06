<?php
// hotel-booking.php
require_once 'config.php';
require_once 'session-check.php';



$conn = getDBConnection();

// Get hotel ID from URL
$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;

// If no hotel ID, show selection page
if ($hotel_id == 0) {
    header("Location: search-hotels.php?error=select_hotel");
    exit();
}

// hotel details'gettt)
$sql = "SELECT * FROM hotels WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Hotel not found
    header("Location: search-hotels.php?error=hotel_not_found");
    exit();
}

$hotel = $result->fetch_assoc();

// correct dateee giveeee
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 2;

// nigttt counttt
$nights = ceil((strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24));

// thenn total
$total_amount = $hotel['price_per_night'] * $nights;

$booking_ref = 'HTL' . date('Ymd') . rand(1000, 9999);

//form submission
$booking_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $special_requests = isset($_POST['special_requests']) ? sanitize($_POST['special_requests']) : '';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'card';
    
    // Validate
    if (empty($full_name) || empty($email) || empty($phone)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Save booking to database
        $insert_sql = "INSERT INTO hotel_bookings (booking_ref, user_id, hotel_id, check_in, check_out, guests, total_amount, booking_status, special_requests) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed', ?)";
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("siissids", $booking_ref, $user_id, $hotel_id, $check_in, $check_out, $guests, $total_amount, $special_requests);
        
        if ($insert_stmt->execute()) {
            $booking_success = true;
            $booking_id = $insert_stmt->insert_id;
        } else {
            $error_message = "Booking failed. Please try again.";
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
    <title>Book <?php echo htmlspecialchars($hotel['hotel_name']); ?> - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/hotel-booking.css">
</head>
<body>
    <div class="container">
        <?php if ($booking_success): ?>
            <!-- Success Message -->
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Booking Confirmed! </h2>
                <p>Your hotel room has been successfully booked.</p>
                <div class="booking-ref">
                    Booking Ref: <?php echo $booking_ref; ?>
                </div>
                <p style="margin-bottom: 20px;">A confirmation email has been sent to <?php echo htmlspecialchars($email); ?></p>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <a href="tourist-dashboard.php" style="text-decoration: none; padding: 12px 25px; background: var(--primary); color: white; border-radius: 8px;">My Profile</a>
                    <a href="search-hotels.php" style="text-decoration: none; padding: 12px 25px; background: var(--light); color: var(--secondary); border-radius: 8px;">Book Another Hotel</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Booking Card -->
            <div class="booking-card">
                <!-- Left Side - Hotel Preview -->
                <div class="hotel-preview">
                    <div class="hotel-image">
                        <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                    </div>
                    
                    <h1 class="hotel-name"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h1>
                    
                    <div class="hotel-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($hotel['location']); ?></span>
                    </div>
                    
                    <div class="rating">
                        <div class="stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $hotel['star_rating']): ?>
                                    <i class="fas fa-star"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text"><?php echo $hotel['star_rating']; ?> Star Hotel</span>
                    </div>
                    
                    <?php if (!empty($hotel['description'])): ?>
                    <p style="margin-bottom: 25px; opacity: 0.9;"><?php echo htmlspecialchars(substr($hotel['description'], 0, 150)) . '...'; ?></p>
                    <?php endif; ?>
                    
                    <div class="price-info">
                        <div class="price-per-night">
                            LKR <?php echo number_format($hotel['price_per_night']); ?> <small>/ night</small>
                        </div>
                        <div style="margin-top: 10px; opacity: 0.8;">
                            <i class="fas fa-wifi"></i> Free WiFi • 
                            <i class="fas fa-parking"></i> Parking • 
                            <i class="fas fa-utensils"></i> Restaurant
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Booking Form -->
                <div class="booking-form">
                    <div class="form-header">
                        <h2>Complete Your Booking</h2>
                        <p>Enter your details to confirm your stay</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Date Summary -->
                    <div class="date-summary">
                        <div class="date-item">
                            <div class="date-label">Check-in</div>
                            <div class="date-value"><?php echo date('M d, Y', strtotime($check_in)); ?></div>
                        </div>
                        <div class="date-item">
                            <div class="date-label">Check-out</div>
                            <div class="date-value"><?php echo date('M d, Y', strtotime($check_out)); ?></div>
                        </div>
                        <div class="night-count">
                            <?php echo $nights; ?> <?php echo $nights > 1 ? 'nights' : 'night'; ?>
                        </div>
                    </div>
                    
                    <form method="POST" action="hotel-booking.php?hotel_id=<?php echo $hotel_id; ?>" id="bookingForm">
                        <input type="hidden" name="check_in" value="<?php echo $check_in; ?>">
                        <input type="hidden" name="check_out" value="<?php echo $check_out; ?>">
                        <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Full Name *
                            </label>
                            <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email Address *
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone Number *
                            </label>
                            <input type="tel" name="phone" class="form-control" placeholder="+94 77 123 4567" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-comment"></i> Special Requests (Optional)
                            </label>
                            <textarea name="special_requests" class="form-control" placeholder="Any special requirements? (dietary, accessibility, etc.)"></textarea>
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
                                <div class="payment-option" onclick="selectPayment('cash')">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Pay at Hotel</span>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="card">
                        </div>
                        
                        <!-- Card Details (shown only for card payment) -->
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
                                <span>Room Rate (<?php echo $nights; ?> nights)</span>
                                <span>LKR <?php echo number_format($hotel['price_per_night'] * $nights); ?></span>
                            </div>
                            <div class="price-item">
                                <span>Taxes & Fees</span>
                                <span>LKR <?php echo number_format($hotel['price_per_night'] * $nights * 0.1); ?></span>
                            </div>
                            <div class="price-total">
                                <span>Total Amount</span>
                                <span>LKR <?php echo number_format($total_amount * 1.1); ?></span>
                            </div>
                            <p style="font-size: 0.8rem; color: #999; margin-top: 10px;">* 10% tax included</p>
                        </div>
                        
                        <button type="submit" class="book-btn">
                            <i class="fas fa-lock"></i> Confirm & Pay
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
        // Payment method selection
        function selectPayment(method) {
            // Update hidden input
            document.getElementById('payment_method').value = method;
            
            // Update selected class
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Show/hide card details
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
            
            if (!fullName || !email || !phone) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
        });
    </script>
</body>
</html>