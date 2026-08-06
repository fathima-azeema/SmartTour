<?php
// restaurant-booking.php
require_once 'config.php';
require_once 'session-check.php';

// Optional: Allow guest bookings
// checkLogin();

$conn = getDBConnection();

// Get restaurant ID from URL
$restaurant_id = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : 0;

// If no restaurant ID, redirect to search page
if ($restaurant_id == 0) {
    header("Location: search-restaurants.php?error=select_restaurant");
    exit();
}

// Get restaurant details
$sql = "SELECT * FROM restaurants WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search-restaurants.php?error=restaurant_not_found");
    exit();
}

$restaurant = $result->fetch_assoc();

// Get booking details from URL or set defaults
$booking_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$booking_time = isset($_GET['time']) ? $_GET['time'] : '19:00';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 2;

// Generate booking reference
$booking_ref = 'RST' . date('Ymd') . rand(1000, 9999);

// Handle form submission
$booking_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $special_requests = isset($_POST['special_requests']) ? sanitize($_POST['special_requests']) : '';
    $booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
    $booking_time = isset($_POST['booking_time']) ? $_POST['booking_time'] : '';
    $guests = isset($_POST['guests']) ? (int)$_POST['guests'] : 2;
    
    // Validate
    if (empty($full_name) || empty($email) || empty($phone) || empty($booking_date) || empty($booking_time)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
        $error_message = "Booking date cannot be in the past.";
    } else {
        // Save booking to database
        // First, create restaurant_bookings table if not exists
        $create_table = "CREATE TABLE IF NOT EXISTS restaurant_bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_ref VARCHAR(20) UNIQUE NOT NULL,
            user_id INT,
            restaurant_id INT NOT NULL,
            booking_date DATE NOT NULL,
            booking_time TIME NOT NULL,
            guests INT NOT NULL,
            special_requests TEXT,
            status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
        )";
        $conn->query($create_table);
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        $insert_sql = "INSERT INTO restaurant_bookings (booking_ref, user_id, restaurant_id, booking_date, booking_time, guests, special_requests, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("siissis", $booking_ref, $user_id, $restaurant_id, $booking_date, $booking_time, $guests, $special_requests);
        
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
    <title>Book <?php echo htmlspecialchars($restaurant['restaurant_name']); ?> - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #e67e22; /* Orange for restaurants */
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #fdf5e6;
            --dark: #2c3e50;
            --text: #34495e;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --success: #27ae60;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
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

        .restaurant-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }

        .restaurant-info p {
            margin: 8px 0;
            color: var(--text);
        }

        .restaurant-info i {
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

        /* Restaurant Preview Section */
        .restaurant-preview {
            background: linear-gradient(135deg, var(--primary), #d35400);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .restaurant-image {
            width: 100%;
            height: 200px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .restaurant-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .restaurant-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .restaurant-location {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .cuisine-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 20px;
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

        .price-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin-top: auto;
        }

        .price-level {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .price-level small {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .features {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .features i {
            margin-right: 5px;
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

        /* Date/Time Summary */
        .datetime-summary {
            background: var(--light);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .datetime-item {
            text-align: center;
            flex: 1;
            min-width: 100px;
        }

        .datetime-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .datetime-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
        }

        .guests-count {
            background: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
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
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Time Slots */
        .time-slots {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .time-slot {
            text-align: center;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .time-slot:hover {
            border-color: var(--primary);
            background: rgba(230, 126, 34, 0.05);
        }

        .time-slot.selected {
            border-color: var(--primary);
            background: rgba(230, 126, 34, 0.1);
            font-weight: 600;
        }

        .time-slot input[type="radio"] {
            display: none;
        }

        /* Booking Summary */
        .booking-summary {
            background: var(--light);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
        }

        .summary-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: var(--text);
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #ddd;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--secondary);
        }

        .free-cancellation {
            color: var(--success);
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .free-cancellation i {
            margin-right: 5px;
        }

        /* Book Button */
        .book-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), #d35400);
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
            box-shadow: 0 10px 20px rgba(230, 126, 34, 0.3);
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
            
            .restaurant-preview {
                padding: 30px;
            }
            
            .booking-form {
                padding: 30px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .time-slots {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .datetime-summary {
                flex-direction: column;
                text-align: center;
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
                <h2>Table Booked Successfully! 🎉</h2>
                <p>Your table has been reserved. We'll send you a confirmation email.</p>
                <div class="booking-ref">
                    Booking Ref: <?php echo $booking_ref; ?>
                </div>
                
                <div class="restaurant-info">
                    <p><i class="fas fa-utensils"></i> <strong><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></strong></p>
                    <p><i class="fas fa-calendar"></i> Date: <?php echo date('l, F j, Y', strtotime($booking_date)); ?></p>
                    <p><i class="fas fa-clock"></i> Time: <?php echo date('g:i A', strtotime($booking_time)); ?></p>
                    <p><i class="fas fa-users"></i> Guests: <?php echo $guests; ?> people</p>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="view-bookings.php" style="text-decoration: none; padding: 12px 25px; background: var(--primary); color: white; border-radius: 8px;">
                        <i class="fas fa-calendar-alt"></i> View My Bookings
                    </a>
                    <a href="search-restaurants.php" style="text-decoration: none; padding: 12px 25px; background: var(--light); color: var(--secondary); border-radius: 8px;">
                        <i class="fas fa-search"></i> Find Another Restaurant
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Booking Card -->
            <div class="booking-card">
                <!-- Left Side - Restaurant Preview -->
                <div class="restaurant-preview">
                    <div class="restaurant-image">
                        <img src="<?php echo htmlspecialchars($restaurant['image_url'] ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'); ?>" 
                             alt="<?php echo htmlspecialchars($restaurant['restaurant_name']); ?>">
                    </div>
                    
                    <h1 class="restaurant-name"><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></h1>
                    
                    <div class="restaurant-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($restaurant['location']); ?></span>
                    </div>
                    
                    <span class="cuisine-badge">
                        <i class="fas fa-utensils"></i> <?php echo htmlspecialchars($restaurant['cuisine_type']); ?>
                    </span>
                    
                    <div class="rating">
                        <div class="stars">
                            <?php 
                            $rating = $restaurant['rating'];
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
                        <span class="rating-text"><?php echo number_format($restaurant['rating'], 1); ?> (200+ reviews)</span>
                    </div>
                    
                    <?php if (!empty($restaurant['description'])): ?>
                    <p style="margin-bottom: 25px; opacity: 0.9;"><?php echo htmlspecialchars(substr($restaurant['description'], 0, 150)) . '...'; ?></p>
                    <?php endif; ?>
                    
                    <div class="price-info">
                        <div class="price-level">
                            <?php 
                            for($i = 1; $i <= $restaurant['price_level']; $i++) {
                                echo '$';
                            }
                            ?>
                            <small>price range</small>
                        </div>
                        <div class="features">
                            <?php if ($restaurant['has_wifi']): ?>
                            <span><i class="fas fa-wifi"></i> WiFi</span>
                            <?php endif; ?>
                            <?php if ($restaurant['has_parking']): ?>
                            <span><i class="fas fa-parking"></i> Parking</span>
                            <?php endif; ?>
                            <?php if ($restaurant['has_ac']): ?>
                            <span><i class="fas fa-snowflake"></i> AC</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Booking Form -->
                <div class="booking-form">
                    <div class="form-header">
                        <h2>Reserve Your Table</h2>
                        <p>Complete the details to confirm your reservation</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Date/Time Summary -->
                    <div class="datetime-summary">
                        <div class="datetime-item">
                            <div class="datetime-label">Date</div>
                            <div class="datetime-value" id="displayDate">
                                <?php echo date('M d, Y', strtotime($booking_date)); ?>
                            </div>
                        </div>
                        <div class="datetime-item">
                            <div class="datetime-label">Time</div>
                            <div class="datetime-value" id="displayTime">
                                <?php echo date('g:i A', strtotime($booking_time)); ?>
                            </div>
                        </div>
                        <div class="guests-count" id="displayGuests">
                            <?php echo $guests; ?> <?php echo $guests > 1 ? 'Guests' : 'Guest'; ?>
                        </div>
                    </div>
                    
                    <form method="POST" action="restaurant-booking.php?restaurant_id=<?php echo $restaurant_id; ?>" id="bookingForm">
                        <input type="hidden" name="booking_date" id="booking_date" value="<?php echo $booking_date; ?>">
                        <input type="hidden" name="booking_time" id="booking_time" value="<?php echo $booking_time; ?>">
                        <input type="hidden" name="guests" id="guests_input" value="<?php echo $guests; ?>">
                        
                        <!-- Date Selection -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar"></i> Select Date *
                            </label>
                            <input type="date" name="booking_date_picker" id="datePicker" class="form-control" 
                                   value="<?php echo $booking_date; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clock"></i> Select Time *
                            </label>
                            <div class="time-slots">
                                <label class="time-slot <?php echo $booking_time == '12:00' ? 'selected' : ''; ?>">
                                    <input type="radio" name="time_slot" value="12:00" <?php echo $booking_time == '12:00' ? 'checked' : ''; ?>>
                                    12:00 PM
                                </label>
                                <label class="time-slot <?php echo $booking_time == '13:00' ? 'selected' : ''; ?>">
                                    <input type="radio" name="time_slot" value="13:00" <?php echo $booking_time == '13:00' ? 'checked' : ''; ?>>
                                    1:00 PM
                                </label>
                                <label class="time-slot <?php echo $booking_time == '19:00' ? 'selected' : ''; ?>">
                                    <input type="radio" name="time_slot" value="19:00" <?php echo $booking_time == '19:00' ? 'checked' : ''; ?>>
                                    7:00 PM
                                </label>
                                <label class="time-slot <?php echo $booking_time == '20:00' ? 'selected' : ''; ?>">
                                    <input type="radio" name="time_slot" value="20:00" <?php echo $booking_time == '20:00' ? 'checked' : ''; ?>>
                                    8:00 PM
                                </label>
                            </div>
                        </div>
                        
                        <!-- Guests Selection -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-users"></i> Number of Guests *
                            </label>
                            <select name="guests_select" id="guestsSelect" class="form-control">
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $guests ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> <?php echo $i > 1 ? 'Guests' : 'Guest'; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
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
                                <i class="fas fa-comment"></i> Special Requests (Optional)
                            </label>
                            <textarea name="special_requests" class="form-control" placeholder="Dietary requirements, allergies, special occasions..."></textarea>
                        </div>
                        
                        <!-- Booking Summary -->
                        <div class="booking-summary">
                            <div class="summary-title">Booking Summary</div>
                            <div class="summary-item">
                                <span>Restaurant:</span>
                                <span><strong><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></strong></span>
                            </div>
                            <div class="summary-item">
                                <span>Date:</span>
                                <span><strong id="summaryDate"><?php echo date('M d, Y', strtotime($booking_date)); ?></strong></span>
                            </div>
                            <div class="summary-item">
                                <span>Time:</span>
                                <span><strong id="summaryTime"><?php echo date('g:i A', strtotime($booking_time)); ?></strong></span>
                            </div>
                            <div class="summary-item">
                                <span>Guests:</span>
                                <span><strong id="summaryGuests"><?php echo $guests; ?> people</strong></span>
                            </div>
                            <div class="summary-total">
                                <span>Reservation Fee</span>
                                <span><strong>Free</strong></span>
                            </div>
                            <div class="free-cancellation">
                                <i class="fas fa-check-circle"></i> Free cancellation up to 2 hours before
                            </div>
                        </div>
                        
                        <button type="submit" class="book-btn">
                            <i class="fas fa-calendar-check"></i> Confirm Reservation
                        </button>
                        
                        <p style="text-align: center; margin-top: 15px; font-size: 0.8rem; color: #999;">
                            <i class="fas fa-shield-alt"></i> No payment required now. Pay at the restaurant.
                        </p>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Update summary when form changes
        document.addEventListener('DOMContentLoaded', function() {
            const datePicker = document.getElementById('datePicker');
            const guestsSelect = document.getElementById('guestsSelect');
            const timeSlots = document.querySelectorAll('input[name="time_slot"]');
            
            // Date picker change
            datePicker.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const options = { month: 'short', day: 'numeric', year: 'numeric' };
                const formattedDate = selectedDate.toLocaleDateString('en-US', options);
                
                document.getElementById('displayDate').textContent = formattedDate;
                document.getElementById('summaryDate').textContent = formattedDate;
                document.getElementById('booking_date').value = this.value;
            });
            
            // Guests change
            guestsSelect.addEventListener('change', function() {
                const guests = this.value;
                document.getElementById('displayGuests').textContent = guests + (guests > 1 ? ' Guests' : ' Guest');
                document.getElementById('summaryGuests').textContent = guests + ' people';
                document.getElementById('guests_input').value = guests;
            });
            
            // Time slot change
            timeSlots.forEach(slot => {
                slot.addEventListener('change', function() {
                    const time = this.value;
                    const timeDisplay = this.parentElement.textContent.trim();
                    
                    // Update selected class
                    document.querySelectorAll('.time-slot').forEach(ts => {
                        ts.classList.remove('selected');
                    });
                    this.parentElement.classList.add('selected');
                    
                    // Update displays
                    document.getElementById('displayTime').textContent = timeDisplay;
                    document.getElementById('summaryTime').textContent = timeDisplay;
                    document.getElementById('booking_time').value = time;
                });
            });
            
            // Form validation
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
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
                
                // Phone validation (basic)
                if (phone.length < 10) {
                    e.preventDefault();
                    alert('Please enter a valid phone number');
                    return;
                }
            });
        });
    </script>
</body>
</html>