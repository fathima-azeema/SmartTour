<?php
// guide-dashboard.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'guide') {
    header("Location: login.php");
    exit();
}

// Sample data for demonstration
$guide_name = $_SESSION['first_name'] ?? 'Smith';
$stats = [
    'total_tours' => 24,
    'active_tours' => 8,
    'bookings' => 15,
    'rating' => 4.8,
    'reviews' => 127,
    'earnings' => 185000,
    'pending_earnings' => 25000,
    'completed_tours' => 89
];

$recent_bookings = [
    ['tour' => 'Cultural Triangle Expedition', 'client' => 'Sarah Johnson', 'date' => 'Today', 'time' => '2:00 PM', 'status' => 'confirmed'],
    ['tour' => 'Hill Country Adventure', 'client' => 'Mike Chen', 'date' => 'Tomorrow', 'time' => '9:00 AM', 'status' => 'confirmed'],
    ['tour' => 'Beach Sunset Tour', 'client' => 'Emma Wilson', 'date' => 'Oct 28', 'time' => '4:30 PM', 'status' => 'pending'],
    ['tour' => 'Wildlife Safari', 'client' => 'Robert Brown', 'date' => 'Nov 2', 'time' => '7:00 AM', 'status' => 'confirmed']
];

$recent_reviews = [
    ['rating' => 5, 'client' => 'Maria Garcia', 'comment' => 'Absolutely fantastic guide! Knowledgeable and engaging.', 'date' => '2 days ago'],
    ['rating' => 4, 'client' => 'David Lee', 'comment' => 'Great tour, very informative. Would recommend!', 'date' => '1 week ago'],
    ['rating' => 5, 'client' => 'Lisa Wong', 'comment' => 'Best guide ever! Made our trip unforgettable.', 'date' => '3 days ago']
];

$popular_tours = [
    ['name' => 'Cultural Heritage Walk', 'bookings' => 42, 'revenue' => 125000, 'rating' => 4.9],
    ['name' => 'Adventure Trekking', 'bookings' => 28, 'revenue' => 89000, 'rating' => 4.7],
    ['name' => 'Gourmet Food Tour', 'bookings' => 35, 'revenue' => 105000, 'rating' => 4.8]
];

$earnings_data = [
    'this_month' => 45000,
    'last_month' => 38000,
    'total' => $stats['earnings'],
    'pending' => $stats['pending_earnings']
];

$availability = [
    ['day' => 'Monday', 'status' => 'available', 'slots' => 3],
    ['day' => 'Tuesday', 'status' => 'available', 'slots' => 2],
    ['day' => 'Wednesday', 'status' => 'full', 'slots' => 0],
    ['day' => 'Thursday', 'status' => 'available', 'slots' => 4],
    ['day' => 'Friday', 'status' => 'available', 'slots' => 1],
    ['day' => 'Saturday', 'status' => 'full', 'slots' => 0],
    ['day' => 'Sunday', 'status' => 'available', 'slots' => 2]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Guide Dashboard - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/guide-dashboard.css">

</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="logo-text">GuidePro</span>
                </div>
                <div class="user-profile">
                    <div class="avatar">
                        <?php echo strtoupper(substr($guide_name, 0, 1)); ?>
                        <span class="online-status"></span>
                    </div>
                    <div class="user-info">
                        <h3>Guide <?php echo htmlspecialchars($guide_name); ?></h3>
                        <span class="user-role">Certified Guide</span>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <span><?php echo $stats['rating']; ?></span>
                            <small>(<?php echo $stats['reviews']; ?> reviews)</small>
                        </div>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="#dashboard">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#tours">
                            <i class="fas fa-route"></i>
                            <span>Tour Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#bookings">
                            <i class="fas fa-calendar-check"></i>
                            <span>Current Bookings</span>
                            <span class="badge"><?php echo $stats['bookings']; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#reviews">
                            <i class="fas fa-star"></i>
                            <span>Reviews & Ratings</span>
                            <span class="badge"><?php echo $stats['reviews']; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#earnings">
                            <i class="fas fa-coins"></i>
                            <span>Earnings</span>
                        </a>
                    </li>
                    <li>
                        <a href="#availability">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Set Availability</span>
                        </a>
                    </li>
                    <li>
                        <a href="#profile">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="index.php">
                    <i class="fas fa-globe"></i>
                    <span>Home Page</span>
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <h1>Welcome, Guide <?php echo htmlspecialchars($guide_name); ?>! <span class="welcome-emoji">🗺️</span></h1>
                    <p>Manage your tours, bookings, and earnings all in one place.</p>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <button class="btn-notification">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div class="quick-create">
                            <button class="btn-create" onclick="createNewTour()">
                                <i class="fas fa-plus"></i>
                                <span>Create Tour</span>
                            </button>
                        </div>
                        <div class="date-display">
                            <i class="fas fa-clock"></i>
                            <span><?php echo date('l, F j, Y'); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['active_tours']; ?></h3>
                        <p>Active Tours</p>
                        <span class="stat-trend up"><i class="fas fa-arrow-up"></i> 12%</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['bookings']; ?></h3>
                        <p>Current Bookings</p>
                        <span class="stat-trend up"><i class="fas fa-arrow-up"></i> 8%</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['rating']; ?></h3>
                        <p>Average Rating</p>
                        <span class="stat-trend stable"><i class="fas fa-minus"></i> Stable</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <h3>LKR <?php echo number_format($stats['earnings'], 0); ?></h3>
                        <p>Total Earnings</p>
                        <span class="stat-trend up"><i class="fas fa-arrow-up"></i> 23%</span>
                    </div>
                </div>
            </div>

            <!-- Dashboard Sections -->
            <div class="dashboard-sections">
                <!-- Tour Management Section -->
                <section id="tours" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-route"></i> Tour Management</h2>
                        <div class="section-actions">
                            <button class="btn-primary" onclick="createNewTour()">
                                <i class="fas fa-plus"></i> Create New Tour
                            </button>
                        </div>
                    </div>
                    
                    <div class="section-content">
                        <div class="tours-overview">
                            <div class="overview-card">
                                <h3>Tour Statistics</h3>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['total_tours']; ?></span>
                                        <span class="stat-label">Total Tours</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['active_tours']; ?></span>
                                        <span class="stat-label">Active Now</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['completed_tours']; ?></span>
                                        <span class="stat-label">Completed</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['bookings']; ?></span>
                                        <span class="stat-label">Bookings</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="popular-tours">
                                <h3>Most Popular Tours</h3>
                                <div class="tours-list">
                                    <?php foreach($popular_tours as $tour): ?>
                                    <div class="tour-item">
                                        <div class="tour-info">
                                            <h4><?php echo $tour['name']; ?></h4>
                                            <div class="tour-stats">
                                                <span><i class="fas fa-calendar-check"></i> <?php echo $tour['bookings']; ?> bookings</span>
                                                <span><i class="fas fa-coins"></i> LKR <?php echo number_format($tour['revenue'], 0); ?></span>
                                            </div>
                                        </div>
                                        <div class="tour-rating">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo $tour['rating']; ?></span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quick-actions">
                            <h3>Quick Actions</h3>
                            <div class="action-buttons">
                                <button class="action-btn" onclick="createNewTour()">
                                    <i class="fas fa-plus-circle"></i> Create New Tour
                                </button>
                                <button class="action-btn" onclick="viewAllTours()">
                                    <i class="fas fa-list"></i> My Tours
                                </button>
                                <button class="action-btn" onclick="editTour()">
                                    <i class="fas fa-edit"></i> Edit Tour
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Current Bookings Section -->
                <section id="bookings" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-check"></i> Current Bookings</h2>
                        <button class="btn-primary" onclick="viewAllBookings()">View All Bookings <i class="fas fa-arrow-right"></i></button>
                    </div>
                    
                    <div class="section-content">
                        <div class="bookings-grid">
                            <?php foreach($recent_bookings as $booking): ?>
                            <div class="booking-card <?php echo $booking['status']; ?>">
                                <div class="booking-header">
                                    <h3><?php echo $booking['tour']; ?></h3>
                                    <span class="booking-status"><?php echo ucfirst($booking['status']); ?></span>
                                </div>
                                <div class="booking-details">
                                    <p><i class="fas fa-user"></i> <?php echo $booking['client']; ?></p>
                                    <p><i class="far fa-calendar"></i> <?php echo $booking['date']; ?> at <?php echo $booking['time']; ?></p>
                                </div>
                                <div class="booking-actions">
                                    <button class="btn-view" onclick="viewBookingDetails()"><i class="fas fa-eye"></i> Details</button>
                                    <?php if($booking['status'] == 'pending'): ?>
                                    <button class="btn-confirm" onclick="confirmBooking()"><i class="fas fa-check"></i> Confirm</button>
                                    <?php endif; ?>
                                    <button class="btn-message" onclick="messageClient()"><i class="fas fa-envelope"></i> Message</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- Reviews & Ratings Section -->
                <section id="reviews" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-star"></i> Reviews & Ratings</h2>
                        <button class="btn-primary" onclick="viewAllReviews()">View All Feedback <i class="fas fa-arrow-right"></i></button>
                    </div>
                    
                    <div class="section-content">
                        <div class="reviews-summary">
                            <div class="rating-overview">
                                <div class="average-rating">
                                    <h3><?php echo $stats['rating']; ?></h3>
                                    <div class="stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= floor($stats['rating'])): ?>
                                            <i class="fas fa-star"></i>
                                            <?php elseif($i == ceil($stats['rating']) && fmod($stats['rating'], 1) > 0): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                            <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <p>Based on <?php echo $stats['reviews']; ?> reviews</p>
                                </div>
                            </div>
                            
                            <div class="recent-reviews">
                                <h3>Recent Reviews</h3>
                                <div class="reviews-list">
                                    <?php foreach($recent_reviews as $review): ?>
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer">
                                                <div class="reviewer-avatar"><?php echo strtoupper(substr($review['client'], 0, 1)); ?></div>
                                                <div>
                                                    <strong><?php echo $review['client']; ?></strong>
                                                    <div class="review-stars">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#ffc107' : '#ddd'; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="review-date"><?php echo $review['date']; ?></span>
                                        </div>
                                        <p class="review-comment">"<?php echo $review['comment']; ?>"</p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Earnings Section -->
                <section id="earnings" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-coins"></i> Earnings</h2>
                        <div class="section-actions">
                            <button class="btn-primary" onclick="withdrawEarnings()"><i class="fas fa-wallet"></i> Withdraw Earnings</button>
                        </div>
                    </div>
                    
                    <div class="section-content">
                        <div class="earnings-overview">
                            <div class="earnings-card">
                                <h3>Earnings Summary</h3>
                                <div class="earnings-stats">
                                    <div class="earnings-item">
                                        <span class="label">Total Earnings</span>
                                        <span class="value">LKR <?php echo number_format($earnings_data['total'], 0); ?></span>
                                    </div>
                                    <div class="earnings-item">
                                        <span class="label">This Month</span>
                                        <span class="value">LKR <?php echo number_format($earnings_data['this_month'], 0); ?></span>
                                    </div>
                                    <div class="earnings-item">
                                        <span class="label">Pending</span>
                                        <span class="value">LKR <?php echo number_format($earnings_data['pending'], 0); ?></span>
                                    </div>
                                    <div class="earnings-item">
                                        <span class="label">Last Month</span>
                                        <span class="value">LKR <?php echo number_format($earnings_data['last_month'], 0); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="earnings-card">
                                <h3>Payment Methods</h3>
                                <div class="earnings-stats">
                                    <div class="earnings-item">
                                        <i class="fab fa-paypal" style="font-size: 1.5rem;"></i>
                                        <span class="label">PayPal</span>
                                        <span class="value">Connected</span>
                                    </div>
                                    <div class="earnings-item">
                                        <i class="fas fa-university" style="font-size: 1.5rem;"></i>
                                        <span class="label">Bank Transfer</span>
                                        <button class="btn-secondary" style="margin-top: 5px;">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Availability Section -->
                <section id="availability" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-alt"></i> Set Availability</h2>
                        <button class="btn-primary" onclick="setAvailability()"><i class="fas fa-edit"></i> Edit Availability</button>
                    </div>
                    
                    <div class="section-content">
                        <div class="week-grid">
                            <?php foreach($availability as $day): ?>
                            <div class="day-card <?php echo $day['status']; ?>">
                                <div class="day-header">
                                    <h4><?php echo $day['day']; ?></h4>
                                    <span class="day-status"><?php echo ucfirst($day['status']); ?></span>
                                </div>
                                <div class="day-slots">
                                    <?php if($day['status'] == 'available'): ?>
                                    <p><i class="fas fa-users"></i> <?php echo $day['slots']; ?> slots available</p>
                                    <?php elseif($day['status'] == 'full'): ?>
                                    <p><i class="fas fa-ban"></i> Fully booked</p>
                                    <?php else: ?>
                                    <p><i class="fas fa-times"></i> Not available</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- Profile Section -->
                <section id="profile" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user-circle"></i> My Profile</h2>
                        <button class="btn-primary" onclick="editProfile()"><i class="fas fa-edit"></i> Edit Profile</button>
                    </div>
                    
                    <div class="section-content">
                        <div class="profile-grid">
                            <div class="profile-info-card">
                                <h3>Guide Information</h3>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label><i class="fas fa-user"></i> Full Name</label>
                                        <p>Guide <?php echo htmlspecialchars($guide_name); ?></p>
                                    </div>
                                    <div class="info-item">
                                        <label><i class="fas fa-envelope"></i> Email</label>
                                        <p>guide@smarttour.com</p>
                                    </div>
                                    <div class="info-item">
                                        <label><i class="fas fa-phone"></i> Phone</label>
                                        <p>+94 77 123 4567</p>
                                    </div>
                                    <div class="info-item">
                                        <label><i class="fas fa-map-marker-alt"></i> Location</label>
                                        <p>Colombo, Sri Lanka</p>
                                    </div>
                                    <div class="info-item">
                                        <label><i class="fas fa-certificate"></i> Certification</label>
                                        <p>Certified Tour Guide - Level 3</p>
                                    </div>
                                    <div class="info-item">
                                        <label><i class="fas fa-language"></i> Languages</label>
                                        <p>English, Sinhala, Tamil</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="profile-stats-card">
                                <h3>Guide Statistics</h3>
                                <div class="stats-grid" style="grid-template-columns: repeat(2,1fr); margin-bottom: 20px;">
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['total_tours']; ?></span>
                                        <span class="stat-label">Tours Created</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['completed_tours']; ?></span>
                                        <span class="stat-label">Tours Completed</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $stats['rating']; ?></span>
                                        <span class="stat-label">Average Rating</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">Top 10%</span>
                                        <span class="stat-label">Platform Ranking</span>
                                    </div>
                                </div>
                                
                                <div class="verification-status">
                                    <h4>Verification Status</h4>
                                    <div class="verification-list">
                                        <div class="verification-item verified">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Email Verified</span>
                                        </div>
                                        <div class="verification-item verified">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Phone Verified</span>
                                        </div>
                                        <div class="verification-item verified">
                                            <i class="fas fa-check-circle"></i>
                                            <span>ID Verified</span>
                                        </div>
                                        <div class="verification-item pending">
                                            <i class="fas fa-clock"></i>
                                            <span>Background Check</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>


    <script>
        //mobileee
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('mobile-open');
        }

        // Smooth scrolling for sidebar navigation
        document.querySelectorAll('.sidebar-nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                if(this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if(targetElement) {
                        document.querySelectorAll('.sidebar-nav li').forEach(li => li.classList.remove('active'));
                        this.parentElement.classList.add('active');
                        
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Close mobile menu on mobile
                        if(window.innerWidth <= 768) {
                            toggleMobileMenu();
                        }
                    }
                }
            });
        });

        // Show/hide sections based on scroll
        const sections = document.querySelectorAll('.dashboard-section');
        const navLinks = document.querySelectorAll('.sidebar-nav li');
        
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if(scrollY >= (sectionTop - 250)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if(link.querySelector('a').getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });

        // Notification bell
        document.querySelector('.btn-notification').addEventListener('click', function() {
            alert('You have 3 new notifications:\n• New booking request\n• New review received\n• Payment processed');
        });

        // Functions
        function createNewTour() { alert('Opening tour creation wizard...'); }
        function viewAllTours() { alert('Showing all your tours...'); }
        function editTour() { alert('Opening tour editor...'); }
        function viewAllBookings() { alert('Showing all bookings...'); }
        function viewAllReviews() { alert('Showing all reviews and feedback...'); }
        function withdrawEarnings() { const amount = prompt('Enter amount to withdraw:'); if(amount) alert(`Withdrawal request for LKR ${amount} submitted!`); }
        function editProfile() { alert('Opening profile editor...'); }
        function viewBookingDetails() { alert('Showing booking details...'); }
        function confirmBooking() { if(confirm('Confirm this booking?')) alert('Booking confirmed!'); }
        function messageClient() { alert('Opening message composer...'); }
        function setAvailability() { alert('Opening availability editor...'); }
    </script>

</body>
</html>