<?php
// tourist-dashboard.php
require_once 'config.php';
require_once 'session-check.php';

// Check if user is logged in and is a tourist
checkLogin();
requireUserType('tourist');

// Get user data
$user = getCurrentUser();

// Database connection
$conn = getDBConnection();

// Fetch upcoming bookings
$user_id = $user['id'];

// Get upcoming hotel bookings
$hotel_sql = "SELECT 
    hb.*,
    h.hotel_name,
    h.location as hotel_location,
    h.image_url as hotel_image
FROM hotel_bookings hb
JOIN hotels h ON hb.hotel_id = h.id
WHERE hb.user_id = ? AND hb.booking_status = 'confirmed' AND hb.check_in >= CURDATE()
ORDER BY hb.check_in ASC
LIMIT 3";

$stmt = $conn->prepare($hotel_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$hotel_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get upcoming tour bookings - FIXED: changed 'status' to 'booking_status'
$tour_sql = "SELECT 
    tb.*,
    t.tour_name,
    t.location as tour_location,
    t.image_url as tour_image,
    t.duration_days
FROM tour_bookings tb
JOIN tours t ON tb.tour_id = t.id
WHERE tb.user_id = ? AND tb.booking_status = 'confirmed' AND tb.tour_date >= CURDATE()
ORDER BY tb.tour_date ASC
LIMIT 3";

$stmt = $conn->prepare($tour_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tour_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get booking statistics - FIXED: changed 'status' to 'booking_status'
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM hotel_bookings WHERE user_id = ?) as total_hotels,
    (SELECT COUNT(*) FROM tour_bookings WHERE user_id = ?) as total_tours,
    (SELECT COUNT(*) FROM hotel_bookings WHERE user_id = ? AND booking_status = 'completed') as completed_hotels,
    (SELECT COUNT(*) FROM tour_bookings WHERE user_id = ? AND booking_status = 'completed') as completed_tours";
    
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

$total_bookings = ($stats['total_hotels'] ?? 0) + ($stats['total_tours'] ?? 0);
$completed_bookings = ($stats['completed_hotels'] ?? 0) + ($stats['completed_tours'] ?? 0);

// Get personalized recommendations - FIXED: check if tours table has rating column, otherwise use default
$rec_sql = "SELECT DISTINCT t.*, 4.5 as rating FROM tours t
            WHERE t.id NOT IN (SELECT tour_id FROM tour_bookings WHERE user_id = ?)
            ORDER BY t.id DESC
            LIMIT 3";

$stmt = $conn->prepare($rec_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recommendations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Dashboard - SmartTour</title>
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
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;
            --gradient: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        .header {
            background: white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
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
            padding: 8px 15px;
            background: var(--light);
            border-radius: 30px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .logout-btn {
            padding: 8px 16px;
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
        }

        /* Welcome Section */
        .welcome-section {
            background: var(--gradient);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            border-radius: 0 0 30px 30px;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .welcome-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .welcome-text p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .date-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px 25px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .date-badge i {
            margin-right: 10px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-icon.bookings { background: rgba(52, 152, 219, 0.1); color: var(--primary); }
        .stat-icon.completed { background: rgba(39, 174, 96, 0.1); color: var(--success); }
        .stat-icon.upcoming { background: rgba(243, 156, 18, 0.1); color: var(--warning); }
        .stat-icon.reviews { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }

        .stat-info h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary);
        }

        /* Quick Actions */
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: var(--secondary);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .action-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .action-icon {
            width: 70px;
            height: 70px;
            background: rgba(52, 152, 219, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary);
        }

        .action-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--secondary);
        }

        .action-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .action-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-link i {
            transition: transform 0.3s ease;
        }

        .action-link:hover i {
            transform: translateX(5px);
        }

        /* Bookings Grid */
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            transition: transform 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .booking-image {
            width: 120px;
            background-size: cover;
            background-position: center;
        }

        .booking-content {
            flex: 1;
            padding: 20px;
        }

        .booking-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .type-hotel { background: #e3f2fd; color: #1565c0; }
        .type-tour { background: #e8f5e9; color: #2e7d32; }

        .booking-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .booking-details {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            font-size: 0.85rem;
            color: #666;
        }

        .booking-details i {
            margin-right: 5px;
            color: var(--primary);
        }

        .booking-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-confirmed { background: #d4edda; color: var(--success); }
        .status-pending { background: #fff3cd; color: var(--warning); }

        .empty-state {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }

        /* Recommendations Grid */
        .rec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .rec-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .rec-card:hover {
            transform: translateY(-5px);
        }

        .rec-image {
            height: 150px;
            background-size: cover;
            background-position: center;
        }

        .rec-content {
            padding: 20px;
        }

        .rec-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .rec-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .rec-price {
            font-weight: 700;
            color: var(--primary);
        }

        .rec-rating {
            color: #ffc107;
        }

        .rec-btn {
            width: 100%;
            padding: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .rec-btn:hover {
            background: #2980b9;
        }

        /* Emergency Section */
        .emergency-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #c0392b 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 40px;
        }

        .emergency-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .emergency-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .emergency-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .emergency-text h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .emergency-text p {
            opacity: 0.9;
        }

        .sos-button {
            padding: 15px 30px;
            background: white;
            color: var(--danger);
            border: none;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sos-button:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .emergency-contacts {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
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
            
            .welcome-content {
                flex-direction: column;
                text-align: center;
            }
            
            .emergency-content {
                flex-direction: column;
                text-align: center;
            }
            
            .emergency-left {
                flex-direction: column;
            }
            
            .emergency-contacts {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .booking-card {
                flex-direction: column;
            }
            
            .booking-image {
                width: 100%;
                height: 150px;
            }
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate {
            animation: slideIn 0.5s ease forwards;
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
                    <a href="tourist-dashboard.php" class="active">Dashboard</a>
                    <a href="search-hotels.php">Hotels</a>
                    <a href="search-restaurants.php">Restaurants</a>
                    <a href="search-tours.php">Tours</a>
                    <a href="view-bookings.php">My Bookings</a>
                </nav>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($user['first_name'] . ' ' . substr($user['last_name'], 0, 1) . '.'); ?></span>
                    </div>
                    <button class="logout-btn" onclick="window.location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="container">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h1>
                    <p>Ready for your next Sri Lankan adventure? Let's explore together.</p>
                </div>
                <div class="date-badge">
                    <i class="far fa-calendar-alt"></i>
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card animate" style="animation-delay: 0.1s">
                <div class="stat-icon bookings">
                    <i class="fas fa-suitcase"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Bookings</h3>
                    <div class="stat-number"><?php echo $total_bookings; ?></div>
                </div>
            </div>
            
            <div class="stat-card animate" style="animation-delay: 0.2s">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Completed Trips</h3>
                    <div class="stat-number"><?php echo $completed_bookings; ?></div>
                </div>
            </div>
            
            <div class="stat-card animate" style="animation-delay: 0.3s">
                <div class="stat-icon upcoming">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Upcoming</h3>
                    <div class="stat-number"><?php echo count($hotel_bookings) + count($tour_bookings); ?></div>
                </div>
            </div>
            
            <div class="stat-card animate" style="animation-delay: 0.4s">
                <div class="stat-icon reviews">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>Reviews Given</h3>
                    <div class="stat-number">0</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <div class="actions-grid">
            <div class="action-card animate" style="animation-delay: 0.5s" onclick="window.location.href='search-hotels.php'">
                <div class="action-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <h3>Book Hotels</h3>
                <p>Find the perfect stay from luxury resorts to boutique hotels</p>
                <span class="action-link">Search Hotels <i class="fas fa-arrow-right"></i></span>
            </div>
            
            <div class="action-card animate" style="animation-delay: 0.6s" onclick="window.location.href='search-restaurants.php'">
                <div class="action-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Reserve Restaurants</h3>
                <p>Discover amazing local cuisine and fine dining experiences</p>
                <span class="action-link">Find Restaurants <i class="fas fa-arrow-right"></i></span>
            </div>
            
            <div class="action-card animate" style="animation-delay: 0.7s" onclick="window.location.href='search-tours.php'">
                <div class="action-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Book Tours</h3>
                <p>Explore cultural landmarks, wildlife, and hidden gems</p>
                <span class="action-link">Browse Tours <i class="fas fa-arrow-right"></i></span>
            </div>
            
            <div class="action-card animate" style="animation-delay: 0.8s" onclick="window.location.href='view-bookings.php'">
                <div class="action-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3>My Bookings</h3>
                <p>View and manage all your upcoming and past bookings</p>
                <span class="action-link">View All <i class="fas fa-arrow-right"></i></span>
            </div>
        </div>

        <!-- Current Bookings -->
        <h2 class="section-title">Your Upcoming Trips</h2>
        <?php if (empty($hotel_bookings) && empty($tour_bookings)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No upcoming trips</h3>
                <p>Ready to plan your next adventure? Browse our tours and hotels!</p>
                <button class="rec-btn" style="width: auto; padding: 12px 30px;" onclick="window.location.href='search-tours.php'">Start Exploring</button>
            </div>
        <?php else: ?>
            <div class="bookings-grid">
                <?php foreach ($hotel_bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-image" style="background-image: url('<?php echo $booking['hotel_image'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'; ?>')"></div>
                    <div class="booking-content">
                        <span class="booking-type type-hotel">Hotel</span>
                        <h3 class="booking-title"><?php echo htmlspecialchars($booking['hotel_name']); ?></h3>
                        <div class="booking-details">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['hotel_location']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d', strtotime($booking['check_in'])); ?> - <?php echo date('M d', strtotime($booking['check_out'])); ?></span>
                        </div>
                        <span class="booking-status status-confirmed">Confirmed</span>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php foreach ($tour_bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-image" style="background-image: url('<?php echo $booking['tour_image'] ?: 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'; ?>')"></div>
                    <div class="booking-content">
                        <span class="booking-type type-tour">Tour</span>
                        <h3 class="booking-title"><?php echo htmlspecialchars($booking['tour_name']); ?></h3>
                        <div class="booking-details">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['tour_location']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($booking['tour_date'])); ?></span>
                        </div>
                        <span class="booking-status status-confirmed">Confirmed</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Recommendations -->
        <h2 class="section-title">Recommended For You</h2>
        <?php if (empty($recommendations)): ?>
            <div class="empty-state">
                <i class="fas fa-compass"></i>
                <h3>No recommendations yet</h3>
                <p>Book your first tour to get personalized recommendations!</p>
            </div>
        <?php else: ?>
            <div class="rec-grid">
                <?php foreach ($recommendations as $rec): ?>
                <div class="rec-card">
                    <div class="rec-image" style="background-image: url('<?php echo $rec['image_url'] ?: 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'; ?>')"></div>
                    <div class="rec-content">
                        <h3 class="rec-title"><?php echo htmlspecialchars($rec['tour_name']); ?></h3>
                        <div class="rec-meta">
                            <span class="rec-price">LKR <?php echo number_format($rec['price_per_person']); ?>/person</span>
                            <span class="rec-rating">
                                <i class="fas fa-star"></i> <?php echo number_format($rec['rating'] ?? 4.5, 1); ?>
                            </span>
                        </div>
                        <button class="rec-btn" onclick="window.location.href='tour-booking.php?tour_id=<?php echo $rec['id']; ?>'">View Details</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Emergency Support Section -->
        <div class="emergency-section">
            <div class="emergency-content">
                <div class="emergency-left">
                    <div class="emergency-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="emergency-text">
                        <h3>24/7 Emergency Support</h3>
                        <p>We're here to help you anytime, anywhere in Sri Lanka</p>
                    </div>
                </div>
                <button class="sos-button" onclick="triggerSOS()">
                    <i class="fas fa-exclamation-triangle"></i> SOS Emergency
                </button>
            </div>
            <div class="emergency-contacts">
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>Police: 119</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-ambulance"></i>
                    <span>Ambulance: 110</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-fire-extinguisher"></i>
                    <span>Fire: 111</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-headset"></i>
                    <span>SmartTour: +94 77 123 4567</span>
                </div>
            </div>
        </div>
    </div>

  <!-- Footer -->
<?php include 'footer.php'; ?>

    <script>
        // SOS Emergency Function
        function triggerSOS() {
            if (confirm('⚠️ EMERGENCY SOS\n\nDo you need immediate assistance? Your location will be shared with emergency services.')) {
                alert('SOS Alert Sent! Emergency services have been notified. Help is on the way.');
                
                // In a real app, this would:
                // 1. Get user's location via GPS
                // 2. Send SOS alert to emergency contacts
                // 3. Notify SmartTour support team
                // 4. Share real-time location
            }
        }

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'slideIn 0.5s ease forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-card, .action-card, .booking-card, .rec-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>