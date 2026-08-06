<?php
// view-bookings.php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php?redirect=view-bookings.php');
}

// Handle cancellation request
if (isset($_POST['cancel_booking'])) {
    $booking_id = (int)$_POST['booking_id'];
    $booking_type = $_POST['booking_type'];
    $conn = getDBConnection();
    
    if ($booking_type == 'hotel') {
        $update_sql = "UPDATE hotel_bookings SET booking_status = 'cancelled', updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    } elseif ($booking_type == 'tour') {
        $update_sql = "UPDATE tour_bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    } elseif ($booking_type == 'restaurant') {
        $update_sql = "UPDATE restaurant_bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
    
    // Redirect to refresh the page
    header("Location: view-bookings.php?cancelled=1");
    exit();
}

// Get database connection
$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get filter parameters
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Fetch hotel bookings
$hotel_bookings = array();
$hotel_sql = "SELECT 
    hb.*,
    h.hotel_name,
    h.location,
    h.image_url,
    h.star_rating,
    'hotel' as booking_type
FROM hotel_bookings hb
JOIN hotels h ON hb.hotel_id = h.id
WHERE hb.user_id = ?
ORDER BY hb.check_in DESC";

$stmt = $conn->prepare($hotel_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$hotel_result = $stmt->get_result();

while ($row = $hotel_result->fetch_assoc()) {
    $hotel_bookings[] = $row;
}
$stmt->close();

// Fetch tour bookings
$tour_bookings = array();
$tour_sql = "SELECT 
    tb.*,
    t.tour_name,
    t.location,
    t.image_url,
    t.duration_days,
    'tour' as booking_type
FROM tour_bookings tb
JOIN tours t ON tb.tour_id = t.id
WHERE tb.user_id = ?
ORDER BY tb.tour_date DESC";

$stmt = $conn->prepare($tour_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tour_result = $stmt->get_result();

while ($row = $tour_result->fetch_assoc()) {
    $tour_bookings[] = $row;
}
$stmt->close();

// Fetch restaurant bookings
$restaurant_bookings = array();
$restaurant_sql = "SELECT 
    rb.*,
    r.restaurant_name,
    r.location,
    r.image_url,
    r.cuisine_type,
    'restaurant' as booking_type
FROM restaurant_bookings rb
JOIN restaurants r ON rb.restaurant_id = r.id
WHERE rb.user_id = ?
ORDER BY rb.booking_date DESC, rb.booking_time DESC";

$stmt = $conn->prepare($restaurant_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$restaurant_result = $stmt->get_result();

while ($row = $restaurant_result->fetch_assoc()) {
    $restaurant_bookings[] = $row;
}
$stmt->close();

$conn->close();

// Merge all bookings
$all_bookings = array_merge($hotel_bookings, $tour_bookings, $restaurant_bookings);

// Sort by date (newest first)
usort($all_bookings, function($a, $b) {
    $date_a = isset($a['check_in']) ? $a['check_in'] : (isset($a['tour_date']) ? $a['tour_date'] : $a['booking_date']);
    $date_b = isset($b['check_in']) ? $b['check_in'] : (isset($b['tour_date']) ? $b['tour_date'] : $b['booking_date']);
    return strtotime($date_b) - strtotime($date_a);
});

// Apply filters
$filtered_bookings = array();
foreach ($all_bookings as $booking) {
    // Determine status field based on type
    $status = isset($booking['booking_status']) ? $booking['booking_status'] : $booking['status'];
    // Filter by status
    if ($filter_status != 'all' && $status != $filter_status) {
        continue;
    }
    // Filter by type
    if ($filter_type != 'all' && $booking['booking_type'] != $filter_type) {
        continue;
    }
    $filtered_bookings[] = $booking;
}

// Calculate statistics
$total_bookings = count($all_bookings);
$confirmed_bookings = 0;
$completed_bookings = 0;
$cancelled_bookings = 0;
$pending_bookings = 0;

foreach ($all_bookings as $booking) {
    $status = isset($booking['booking_status']) ? $booking['booking_status'] : $booking['status'];
    switch($status) {
        case 'confirmed':
            $confirmed_bookings++;
            break;
        case 'completed':
            $completed_bookings++;
            break;
        case 'cancelled':
            $cancelled_bookings++;
            break;
        case 'pending':
            $pending_bookings++;
            break;
    }
}

// Show success message if cancelled
$show_cancelled_message = isset($_GET['cancelled']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --text: #34495e;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;
            --orange: #e67e22; /* for restaurants */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles - same as before */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--secondary);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .logo i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 1.8rem;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .logout-btn {
            padding: 8px 16px;
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Alert Message */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid var(--success);
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid var(--danger);
            color: #721c24;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .stat-card.total i { color: var(--primary); }
        .stat-card.confirmed i { color: var(--success); }
        .stat-card.completed i { color: var(--info); }
        .stat-card.cancelled i { color: var(--danger); }
        .stat-card.pending i { color: var(--warning); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        /* Filters */
        .filters {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary);
        }

        .filter-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            background: white;
        }

        .filter-select:focus {
            border-color: var(--primary);
            outline: none;
        }

        .btn-filter {
            padding: 12px 25px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-reset {
            padding: 12px 25px;
            background: #f8f9fa;
            color: var(--text);
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: #e9ecef;
        }

        /* Booking Cards */
        .bookings-container {
            margin-top: 30px;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-3px);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 15px;
        }

        .booking-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .booking-ref {
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .booking-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .type-hotel {
            background: #e3f2fd;
            color: #1565c0;
        }

        .type-tour {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .type-restaurant {
            background: #fff3e0;
            color: #e67e22;
        }

        .booking-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-confirmed {
            background: #d4edda;
            color: var(--success);
        }

        .status-pending {
            background: #fff3cd;
            color: var(--warning);
        }

        .status-cancelled {
            background: #f8d7da;
            color: var(--danger);
        }

        .status-completed {
            background: #d1ecf1;
            color: var(--info);
        }

        .booking-content {
            padding: 25px;
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .booking-image {
            width: 150px;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .booking-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .booking-details {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            color: var(--secondary);
            font-size: 1rem;
        }

        .detail-value i {
            margin-right: 5px;
            color: var(--primary);
        }

        .booking-actions {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .action-btn.view {
            background: var(--primary);
            color: white;
            border: none;
        }

        .action-btn.view:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .action-btn.cancel {
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .action-btn.cancel:hover {
            background: var(--danger);
            color: white;
        }

        .action-btn.review {
            background: transparent;
            color: var(--success);
            border: 1px solid var(--success);
        }

        .action-btn.review:hover {
            background: var(--success);
            color: white;
        }

        .action-btn.rebook {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .action-btn.rebook:hover {
            background: var(--primary);
            color: white;
        }

        /* No Bookings */
        .no-bookings {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .no-bookings i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-bookings h3 {
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .no-bookings p {
            color: #666;
            margin-bottom: 30px;
        }

        .no-bookings .btn {
            padding: 12px 30px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .no-bookings .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* Cancel Modal */
        .cancel-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .cancel-modal-content {
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 450px;
            position: relative;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cancel-modal-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cancel-modal-header h3 {
            color: var(--danger);
        }

        .cancel-modal-body {
            padding: 20px;
        }

        .cancel-modal-footer {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .booking-content {
                flex-direction: column;
            }
            
            .booking-image {
                width: 100%;
                height: 200px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .booking-actions {
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="logo-text">SmartTour</span>
                </a>
                
                <nav class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="search-hotels.php">Hotels</a>
                    <a href="search-tours.php">Tours</a>
                    <a href="search-restaurants.php">Restaurants</a>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="view-bookings.php" class="active">My Bookings</a>
                </nav>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php 
                            $user = getCurrentUser();
                            echo strtoupper(substr($user['first_name'], 0, 1)); 
                            ?>
                        </div>
                        <span><?php echo htmlspecialchars($user['first_name']); ?></span>
                    </div>
                    <button class="logout-btn" onclick="window.location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>My Bookings</h1>
            <p>View and manage all your hotel, tour, and restaurant reservations</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Success Message -->
            <?php if ($show_cancelled_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>Your booking has been successfully cancelled. A confirmation email has been sent to your registered email address.</span>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <i class="fas fa-receipt"></i>
                    <div class="stat-number"><?php echo $total_bookings; ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card confirmed">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-number"><?php echo $confirmed_bookings; ?></div>
                    <div class="stat-label">Confirmed</div>
                </div>
                <div class="stat-card completed">
                    <i class="fas fa-clock"></i>
                    <div class="stat-number"><?php echo $completed_bookings; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card pending">
                    <i class="fas fa-hourglass-half"></i>
                    <div class="stat-number"><?php echo $pending_bookings; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card cancelled">
                    <i class="fas fa-times-circle"></i>
                    <div class="stat-number"><?php echo $cancelled_bookings; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="view-bookings.php">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label><i class="fas fa-filter"></i> Booking Status</label>
                            <select name="status" class="filter-select">
                                <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All Status</option>
                                <option value="confirmed" <?php echo $filter_status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="completed" <?php echo $filter_status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $filter_status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label><i class="fas fa-tag"></i> Booking Type</label>
                            <select name="type" class="filter-select">
                                <option value="all" <?php echo $filter_type == 'all' ? 'selected' : ''; ?>>All Types</option>
                                <option value="hotel" <?php echo $filter_type == 'hotel' ? 'selected' : ''; ?>>Hotels</option>
                                <option value="tour" <?php echo $filter_type == 'tour' ? 'selected' : ''; ?>>Tours</option>
                                <option value="restaurant" <?php echo $filter_type == 'restaurant' ? 'selected' : ''; ?>>Restaurants</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <button type="submit" class="btn-filter">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="view-bookings.php" class="btn-reset">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bookings List -->
            <div class="bookings-container">
                <?php if (empty($filtered_bookings)): ?>
                    <div class="no-bookings">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Bookings Found</h3>
                        <p><?php echo ($filter_status != 'all' || $filter_type != 'all') ? 'Try changing your filters to see more results.' : 'You haven\'t made any bookings yet. Start exploring!' ; ?></p>
                        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                            <a href="search-hotels.php" class="btn">
                                <i class="fas fa-hotel"></i> Browse Hotels
                            </a>
                            <a href="search-tours.php" class="btn" style="background: var(--success);">
                                <i class="fas fa-map-marked-alt"></i> Browse Tours
                            </a>
                            <a href="search-restaurants.php" class="btn" style="background: var(--orange);">
                                <i class="fas fa-utensils"></i> Browse Restaurants
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($filtered_bookings as $booking): ?>
                        <?php
                        // Determine status field
                        $status = isset($booking['booking_status']) ? $booking['booking_status'] : $booking['status'];
                        $status_class = 'status-' . $status;
                        
                        // Determine dates and duration
                        if ($booking['booking_type'] == 'hotel') {
                            $start_date = date('M d, Y', strtotime($booking['check_in']));
                            $end_date = date('M d, Y', strtotime($booking['check_out']));
                            $duration = ceil((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60*60*24)) . ' nights';
                            $guests = $booking['guests'] . ' guest(s)';
                            $service_name = $booking['hotel_name'];
                            $service_id = $booking['hotel_id'];
                        } elseif ($booking['booking_type'] == 'tour') {
                            $start_date = date('M d, Y', strtotime($booking['tour_date']));
                            $end_date = 'Single Day';
                            $duration = $booking['duration_days'] . ' day(s)';
                            $guests = $booking['participants'] . ' person(s)';
                            $service_name = $booking['tour_name'];
                            $service_id = $booking['tour_id'];
                        } else { // restaurant
                            $start_date = date('M d, Y', strtotime($booking['booking_date']));
                            $end_date = date('g:i A', strtotime($booking['booking_time']));
                            $duration = 'Dinner reservation';
                            $guests = $booking['guests'] . ' guest(s)';
                            $service_name = $booking['restaurant_name'];
                            $service_id = $booking['restaurant_id'];
                        }
                        
                        // Can cancel: confirmed and at least 2 days before
                        $can_cancel = false;
                        $current_date = date('Y-m-d');
                        if ($status == 'confirmed') {
                            if ($booking['booking_type'] == 'hotel') {
                                $check_date = $booking['check_in'];
                            } elseif ($booking['booking_type'] == 'tour') {
                                $check_date = $booking['tour_date'];
                            } else {
                                $check_date = $booking['booking_date'];
                            }
                            $days_diff = ceil((strtotime($check_date) - strtotime($current_date)) / (60*60*24));
                            if ($days_diff >= 2) {
                                $can_cancel = true;
                            }
                        }
                        
                        $can_review = ($status == 'completed');
                        ?>
                        
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-info">
                                    <span class="booking-ref">
                                        <i class="fas fa-hashtag"></i> <?php echo $booking['booking_ref']; ?>
                                    </span>
                                    <span class="booking-type type-<?php echo $booking['booking_type']; ?>">
                                        <i class="fas fa-<?php echo $booking['booking_type'] == 'hotel' ? 'hotel' : ($booking['booking_type'] == 'tour' ? 'map-marked-alt' : 'utensils'); ?>"></i>
                                        <?php echo ucfirst($booking['booking_type']); ?>
                                    </span>
                                    <span class="booking-status <?php echo $status_class; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </div>
                                <?php if ($booking['booking_type'] != 'restaurant'): ?>
                                <div class="booking-price">
                                    <strong>LKR <?php echo number_format($booking['total_amount'], 2); ?></strong>
                                </div>
                                <?php else: ?>
                                <div class="booking-price">
                                    <span style="color: var(--success);"><i class="fas fa-check-circle"></i> Free booking</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="booking-content">
                                <div class="booking-image">
                                    <img src="<?php echo $booking['image_url'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=150&h=150&fit=crop'; ?>" 
                                         alt="<?php echo htmlspecialchars($service_name); ?>">
                                </div>
                                
                                <div class="booking-details">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-<?php echo $booking['booking_type'] == 'hotel' ? 'building' : ($booking['booking_type'] == 'tour' ? 'map' : 'utensils'); ?>"></i> 
                                            <?php echo ucfirst($booking['booking_type']); ?> Name
                                        </div>
                                        <div class="detail-value">
                                            <?php echo htmlspecialchars($service_name); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-map-marker-alt"></i> Location
                                        </div>
                                        <div class="detail-value">
                                            <?php echo htmlspecialchars($booking['location']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-calendar-alt"></i> <?php echo $booking['booking_type'] == 'hotel' ? 'Check-in / Check-out' : ($booking['booking_type'] == 'tour' ? 'Tour Date' : 'Reservation Date'); ?>
                                        </div>
                                        <div class="detail-value">
                                            <?php echo $start_date; ?>
                                            <?php if ($end_date != 'Single Day' && $booking['booking_type'] != 'restaurant'): ?>
                                                <br><small>to <?php echo $end_date; ?></small>
                                            <?php elseif ($booking['booking_type'] == 'restaurant'): ?>
                                                <br><small>at <?php echo $end_date; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-clock"></i> Duration
                                        </div>
                                        <div class="detail-value">
                                            <?php echo $duration; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-users"></i> <?php echo $booking['booking_type'] == 'hotel' ? 'Guests' : ($booking['booking_type'] == 'tour' ? 'Participants' : 'Guests'); ?>
                                        </div>
                                        <div class="detail-value">
                                            <?php echo $guests; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-calendar-week"></i> Booked On
                                        </div>
                                        <div class="detail-value">
                                            <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-actions">
                                <button class="action-btn view" onclick="viewBooking('<?php echo $booking['booking_ref']; ?>', '<?php echo $booking['booking_type']; ?>')">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                
                                <?php if ($can_cancel): ?>
                                    <button class="action-btn cancel" onclick="showCancelModal(<?php echo $booking['id']; ?>, '<?php echo $booking['booking_type']; ?>')">
                                        <i class="fas fa-times"></i> Cancel Booking
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($can_review): ?>
                                    <button class="action-btn review" onclick="leaveReview('<?php echo $booking['id']; ?>', '<?php echo $booking['booking_type']; ?>')">
                                        <i class="fas fa-star"></i> Write a Review
                                    </button>
                                <?php endif; ?>
                                
                                <button class="action-btn rebook" onclick="rebook('<?php echo $booking['booking_type']; ?>', '<?php echo $service_id; ?>')">
                                    <i class="fas fa-redo-alt"></i> Book Again
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="cancel-modal">
        <div class="cancel-modal-content">
            <div class="cancel-modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Cancellation</h3>
                <button class="close-modal" onclick="closeCancelModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="view-bookings.php" id="cancelForm">
                <div class="cancel-modal-body">
                    <p>Are you sure you want to cancel this booking?</p>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 15px;">
                        <small><i class="fas fa-info-circle"></i> Cancellation Policy:</small>
                        <ul style="margin-top: 10px; padding-left: 20px;">
                            <li>Full refund if cancelled 7+ days before check-in</li>
                            <li>50% refund if cancelled 2-6 days before check-in</li>
                            <li>No refund for cancellations within 48 hours</li>
                        </ul>
                    </div>
                    <input type="hidden" name="booking_id" id="cancel_booking_id">
                    <input type="hidden" name="booking_type" id="cancel_booking_type">
                    <input type="hidden" name="cancel_booking" value="1">
                </div>
                <div class="cancel-modal-footer">
                    <button type="button" class="btn-reset" onclick="closeCancelModal()">No, Go Back</button>
                    <button type="submit" class="action-btn cancel" style="background: var(--danger); color: white;">Yes, Cancel Booking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="bookingModal" class="cancel-modal">
        <div class="cancel-modal-content">
            <div class="cancel-modal-header">
                <h3><i class="fas fa-info-circle"></i> Booking Details</h3>
                <button class="close-modal" onclick="closeBookingModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div class="cancel-modal-body" id="modalBody">
                <!-- Content loaded via JavaScript -->
            </div>
            <div class="cancel-modal-footer">
                <button class="btn-reset" onclick="closeBookingModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        // Show cancel confirmation modal
        function showCancelModal(bookingId, bookingType) {
            document.getElementById('cancel_booking_id').value = bookingId;
            document.getElementById('cancel_booking_type').value = bookingType;
            document.getElementById('cancelModal').style.display = 'flex';
        }
        
        // Close cancel modal
        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }
        
        // View booking details
        function viewBooking(bookingRef, bookingType) {
            const modal = document.getElementById('bookingModal');
            const modalBody = document.getElementById('modalBody');
            
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                    <p>Loading booking details...</p>
                </div>
            `;
            modal.style.display = 'flex';
            
            setTimeout(() => {
                modalBody.innerHTML = `
                    <p><strong>Booking Reference:</strong> ${bookingRef}</p>
                    <hr>
                    <p><i class="fas fa-info-circle"></i> Booking Details:</p>
                    <ul style="margin-top: 10px; padding-left: 20px;">
                        <li>Full booking information would appear here</li>
                        <li>Payment status and transaction details</li>
                        <li>Cancellation policy and terms</li>
                        <li>Contact information for the service provider</li>
                        <li>Check-in instructions and requirements</li>
                    </ul>
                    <p style="color: #666; margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                        <i class="fas fa-envelope"></i> A confirmation email has been sent to your registered email address.
                    </p>
                `;
            }, 500);
        }
        
        // Close booking modal
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
        
        // Leave review
        function leaveReview(bookingId, type) {
            window.location.href = `leave-review.php?booking_id=${bookingId}&type=${type}`;
        }
        
        // Rebook
        function rebook(type, serviceId) {
            if (type === 'hotel') {
                window.location.href = `hotel-booking.php?hotel_id=${serviceId}`;
            } else if (type === 'tour') {
                window.location.href = `tour-booking.php?tour_id=${serviceId}`;
            } else if (type === 'restaurant') {
                window.location.href = `restaurant-booking.php?restaurant_id=${serviceId}`;
            }
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const cancelModal = document.getElementById('cancelModal');
            const bookingModal = document.getElementById('bookingModal');
            if (event.target == cancelModal) {
                closeCancelModal();
            }
            if (event.target == bookingModal) {
                closeBookingModal();
            }
        }
    </script>
</body>
</html>