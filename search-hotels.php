<?php
// search-hotels.php
require_once 'config.php';

// Get database connection
$conn = getDBConnection();

// Get selected dates from URL
$selected_checkin = isset($_GET['checkin']) ? $_GET['checkin'] : date('Y-m-d');
$selected_checkout = isset($_GET['checkout']) ? $_GET['checkout'] : date('Y-m-d', strtotime('+1 day'));
$selected_guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 2;

// Pagination settings
$hotels_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $hotels_per_page;

// Build search query based on filters
$where_clauses = array();
$params = array();
$types = "";

// Location filter
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
if (!empty($location)) {
    $where_clauses[] = "(hotel_name LIKE ? OR location LIKE ? OR address LIKE ?)";
    $search_term = "%$location%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Price range filter
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 50000;
if ($min_price > 0 || $max_price < 50000) {
    $where_clauses[] = "price_per_night BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types .= "dd";
}

// Star rating filter
$star_rating = isset($_GET['star_rating']) ? (int)$_GET['star_rating'] : 0;
if ($star_rating > 0) {
    $where_clauses[] = "star_rating >= ?";
    $params[] = $star_rating;
    $types .= "i";
}

// Build WHERE clause
$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM hotels $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_hotels = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_hotels / $hotels_per_page);
$count_stmt->close();

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$order_by = "ORDER BY ";
switch ($sort) {
    case 'price_low':
        $order_by .= "price_per_night ASC";
        break;
    case 'price_high':
        $order_by .= "price_per_night DESC";
        break;
    case 'rating':
        $order_by .= "star_rating DESC";
        break;
    case 'name':
        $order_by .= "hotel_name ASC";
        break;
    default:
        $order_by .= "id DESC";
}

// Get hotels for current page
$sql = "SELECT * FROM hotels $where_sql $order_by LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $all_params = array_merge($params, [$hotels_per_page, $offset]);
    $types .= "ii";
    $stmt->bind_param($types, ...$all_params);
} else {
    $stmt->bind_param("ii", $hotels_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

$hotels = array();
while ($row = $result->fetch_assoc()) {
    $hotels[] = $row;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Hotels - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/style.css">
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
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
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

        .login-btn {
            padding: 8px 20px;
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .login-btn:hover {
            background: var(--primary);
            color: white;
        }

        .signup-btn {
            padding: 8px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .signup-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* Hero Search Section */
        .search-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .search-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .search-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 40px;
        }

        /* Search Form */
        .search-form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: var(--shadow);
            max-width: 1000px;
            margin: 0 auto;
        }

        .search-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .search-group {
            text-align: left;
        }

        .search-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary);
        }

        .search-group input,
        .search-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-group input:focus,
        .search-group select:focus {
            border-color: var(--primary);
            outline: none;
        }

        .search-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .search-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* Main Content Area */
        .main-content {
            padding: 40px 0;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }

        /* Sidebar Filters */
        .filters-sidebar {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--shadow);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .filter-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .filter-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-options {
            list-style: none;
        }

        .filter-option {
            margin-bottom: 10px;
        }

        .filter-option label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text);
        }

        .filter-option input[type="checkbox"],
        .filter-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .price-range {
            padding: 10px 0;
        }

        .price-inputs {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .price-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }

        .star-rating {
            color: #ffc107;
            font-size: 0.9rem;
        }

        /* Results Header */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .results-count {
            font-weight: 500;
        }

        .results-count strong {
            color: var(--primary);
            font-size: 1.2rem;
        }

        .sort-select {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
        }

        /* Hotel Cards Grid */
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .hotel-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
        }

        .hotel-image {
            height: 220px;
            position: relative;
            overflow: hidden;
        }

        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .hotel-card:hover .hotel-image img {
            transform: scale(1.1);
        }

        .hotel-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .hotel-content {
            padding: 20px;
        }

        .hotel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .hotel-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary);
            margin-right: 10px;
        }

        .hotel-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            white-space: nowrap;
        }

        .hotel-price span {
            font-size: 0.75rem;
            font-weight: normal;
            color: #999;
        }

        .hotel-location {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .hotel-location i {
            color: var(--primary);
        }

        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .stars {
            color: #ffc107;
            font-size: 0.9rem;
        }

        .rating-text {
            color: #666;
            font-size: 0.8rem;
        }

        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .amenity-tag {
            background: var(--light);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .amenity-tag i {
            color: var(--primary);
            font-size: 0.7rem;
        }

        .hotel-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .view-btn {
            flex: 1;
            padding: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .view-btn:hover {
            background: #2980b9;
        }

        .book-now-btn {
            flex: 1;
            padding: 10px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .book-now-btn:hover {
            background: #219653;
            transform: translateY(-2px);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .page-link {
            padding: 10px 15px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover,
        .page-link.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Modal */
        .modal {
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

        .modal-content {
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .no-results i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-results h3 {
            color: var(--secondary);
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            background: var(--secondary);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #cbd5e1;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
            
            .filters-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .search-row {
                grid-template-columns: 1fr;
            }
            
            .hotels-grid {
                grid-template-columns: 1fr;
            }
            
            .results-header {
                flex-direction: column;
                gap: 15px;
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
                    <a href="search-hotels.php" class="active">Hotels</a>
                    <a href="search-tours.php">Tours</a>
                    <a href="about-us.php">About</a>
                    <a href="contact.php">Contact</a>
                </nav>
                
                <div class="user-menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="login-btn">Dashboard</a>
                        <a href="logout.php" class="signup-btn">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="login-btn">Login</a>
                        <a href="signup.php" class="signup-btn">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Hero Section -->
    <section class="search-hero">
        <div class="container">
            <h1>Find Your Perfect Stay in Sri Lanka</h1>
            <p>Discover luxury resorts, boutique hotels, and cozy accommodations across the island</p>
            
            <!-- Search Form -->
            <div class="search-form-container">
                <form method="GET" action="search-hotels.php" id="searchForm">
                    <div class="search-row">
                        <div class="search-group">
                            <label><i class="fas fa-map-marker-alt"></i> Destination</label>
                            <input type="text" name="location" placeholder="Where are you going?" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-calendar"></i> Check-in</label>
                            <input type="text" id="checkin" name="checkin" class="datepicker" placeholder="Select date" value="<?php echo $selected_checkin; ?>">
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-calendar"></i> Check-out</label>
                            <input type="text" id="checkout" name="checkout" class="datepicker" placeholder="Select date" value="<?php echo $selected_checkout; ?>">
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-users"></i> Guests</label>
                            <select name="guests" id="guests">
                                <option value="1" <?php echo $selected_guests == 1 ? 'selected' : ''; ?>>1 Guest</option>
                                <option value="2" <?php echo $selected_guests == 2 ? 'selected' : ''; ?>>2 Guests</option>
                                <option value="3" <?php echo $selected_guests == 3 ? 'selected' : ''; ?>>3 Guests</option>
                                <option value="4" <?php echo $selected_guests == 4 ? 'selected' : ''; ?>>4 Guests</option>
                                <option value="5" <?php echo $selected_guests == 5 ? 'selected' : ''; ?>>5+ Guests</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search Hotels
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- Filters Sidebar -->
                <aside class="filters-sidebar">
                    <form method="GET" action="search-hotels.php" id="filterForm">
                        <!-- Keep search parameters -->
                        <input type="hidden" name="checkin" value="<?php echo $selected_checkin; ?>">
                        <input type="hidden" name="checkout" value="<?php echo $selected_checkout; ?>">
                        <input type="hidden" name="guests" value="<?php echo $selected_guests; ?>">
                        
                        <!-- Price Range Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-rupee-sign"></i> Price Range (per night)
                            </h3>
                            <div class="price-range">
                                <div class="price-inputs">
                                    <input type="number" name="min_price" class="price-input" placeholder="Min" value="<?php echo $min_price ?: ''; ?>">
                                    <input type="number" name="max_price" class="price-input" placeholder="Max" value="<?php echo $max_price != 50000 ? $max_price : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Star Rating Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-star"></i> Star Rating
                            </h3>
                            <div class="filter-options">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="star_rating" value="<?php echo $i; ?>" <?php echo $star_rating == $i ? 'checked' : ''; ?>>
                                        <span class="star-rating">
                                            <?php for($j = 0; $j < $i; $j++): ?>★<?php endfor; ?>
                                            <?php for($j = 0; $j < 5 - $i; $j++): ?>☆<?php endfor; ?>
                                        </span>
                                    </label>
                                </div>
                                <?php endfor; ?>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="star_rating" value="0" <?php echo $star_rating == 0 ? 'checked' : ''; ?>>
                                        <span>All Ratings</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="search-btn" style="margin-top: 10px;">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </form>
                </aside>

                <!-- Results Section -->
                <div class="results-section">
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-count">
                            <strong><?php echo $total_hotels; ?></strong> hotels found
                        </div>
                        <div>
                            <select class="sort-select" name="sort" form="searchForm" onchange="document.getElementById('searchForm').submit()">
                                <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hotels Grid -->
                    <?php if (empty($hotels)): ?>
                        <div class="no-results">
                            <i class="fas fa-hotel"></i>
                            <h3>No Hotels Found</h3>
                            <p>Try adjusting your filters or search criteria</p>
                        </div>
                    <?php else: ?>
                        <div class="hotels-grid" id="hotelsGrid">
                            <?php foreach ($hotels as $hotel): ?>
                            <div class="hotel-card">
                                <div class="hotel-image">
                                    <img src="<?php echo $hotel['image_url'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'; ?>" 
                                         alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                    <?php if ($hotel['star_rating'] >= 5): ?>
                                    <div class="hotel-badge">Luxury</div>
                                    <?php elseif ($hotel['price_per_night'] <= 5000): ?>
                                    <div class="hotel-badge" style="background: var(--success);">Budget</div>
                                    <?php endif; ?>
                                </div>
                                <div class="hotel-content">
                                    <div class="hotel-header">
                                        <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h3>
                                        <div class="hotel-price">
                                            LKR <?php echo number_format($hotel['price_per_night']); ?>
                                            <span>/night</span>
                                        </div>
                                    </div>
                                    
                                    <div class="hotel-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($hotel['location']); ?>
                                    </div>
                                    
                                    <div class="hotel-rating">
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
                                    
                                    <div class="hotel-amenities">
                                        <?php 
                                        $amenities = explode(',', $hotel['amenities']);
                                        $display_amenities = array_slice($amenities, 0, 3);
                                        foreach ($display_amenities as $amenity):
                                        ?>
                                        <span class="amenity-tag">
                                            <i class="fas fa-<?php echo $amenity == 'wifi' ? 'wifi' : ($amenity == 'pool' ? 'swimming-pool' : ($amenity == 'parking' ? 'parking' : 'check')); ?>"></i>
                                            <?php echo ucfirst($amenity); ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="hotel-actions">
                                        <button class="view-btn" onclick="viewHotel(<?php echo $hotel['id']; ?>, '<?php echo htmlspecialchars($hotel['hotel_name']); ?>')">
                                            <i class="fas fa-info-circle"></i> Details
                                        </button>
                                        <a href="hotel-booking.php?hotel_id=<?php echo $hotel['id']; ?>&check_in=<?php echo $selected_checkin; ?>&check_out=<?php echo $selected_checkout; ?>&guests=<?php echo $selected_guests; ?>" class="book-now-btn">
                                            <i class="fas fa-calendar-check"></i> Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&location=<?php echo urlencode($location); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&star_rating=<?php echo $star_rating; ?>&sort=<?php echo $sort; ?>&checkin=<?php echo $selected_checkin; ?>&checkout=<?php echo $selected_checkout; ?>&guests=<?php echo $selected_guests; ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            for($i = $start_page; $i <= $end_page; $i++): 
                            ?>
                            <a href="?page=<?php echo $i; ?>&location=<?php echo urlencode($location); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&star_rating=<?php echo $star_rating; ?>&sort=<?php echo $sort; ?>&checkin=<?php echo $selected_checkin; ?>&checkout=<?php echo $selected_checkout; ?>&guests=<?php echo $selected_guests; ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?>&location=<?php echo urlencode($location); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&star_rating=<?php echo $star_rating; ?>&sort=<?php echo $sort; ?>&checkin=<?php echo $selected_checkin; ?>&checkout=<?php echo $selected_checkout; ?>&guests=<?php echo $selected_guests; ?>" class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="hotelModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Hotel Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded -->
            </div>
            <div class="modal-footer">
                <button class="login-btn" onclick="closeModal()">Close</button>
                <a href="#" id="modalBookLink" class="signup-btn">Book Now</a>
            </div>
        </div>
    </div>

<!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">Smart<span>Tour</span></div>
                    <p>Your trusted partner for unforgettable Sri Lankan adventures. Discover amazing destinations with our curated tours and expert guides.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="about-us.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                        <li><a href="tours.php"><i class="fas fa-map-marked-alt"></i> Tour Packages</a></li>
                        <li><a href="hotels.php"><i class="fas fa-hotel"></i> Hotels</a></li>
                        <li><a href="restaurants.php"><i class="fas fa-utensils"></i> Restaurants</a></li>
                        <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <div>123 Galle Road, Colombo 03, Sri Lanka</div>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <div>+94 11 234 5678</div>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <div>info@smarttour.lk</div>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <div>Mon - Sun: 24/7 Customer Support</div>
                        </li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest travel deals and destination guides.</p>
                    <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                        <input type="email" class="newsletter-input" placeholder="Your email address" required>
                        <button type="submit" class="newsletter-btn"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa" title="Visa"></i>
                        <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                        <i class="fab fa-cc-amex" title="American Express"></i>
                        <i class="fab fa-cc-paypal" title="PayPal"></i>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    &copy; 2023 SmartTour. All rights reserved.
                </div>
                <div>
                    <a href="privacy.php">Privacy Policy</a>
                    <a href="terms.php">Terms of Service</a>
                    <a href="sitemap.php">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        let currentHotelId = null;
        let currentHotelName = null;

        // View hotel details
        function viewHotel(hotelId, hotelName) {
            currentHotelId = hotelId;
            currentHotelName = hotelName;
            
            const modal = document.getElementById('hotelModal');
            const modalBody = document.getElementById('modalBody');
            const modalBookLink = document.getElementById('modalBookLink');
            
            // Set book now link
            const checkin = document.getElementById('checkin')?.value || '<?php echo $selected_checkin; ?>';
            const checkout = document.getElementById('checkout')?.value || '<?php echo $selected_checkout; ?>';
            const guests = document.getElementById('guests')?.value || '<?php echo $selected_guests; ?>';
            
            modalBookLink.href = `hotel-booking.php?hotel_id=${hotelId}&check_in=${checkin}&check_out=${checkout}&guests=${guests}`;
            
            // Show modal
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                    <p>Loading hotel details...</p>
                </div>
            `;
            modal.style.display = 'flex';
            
            // Simulate loading (in real app, fetch from server)
            setTimeout(() => {
                modalBody.innerHTML = `
                    <h3>${hotelName}</h3>
                    <p>Click "Book Now" to proceed with your booking.</p>
                    <hr>
                    <p><strong>Check-in:</strong> ${checkin}</p>
                    <p><strong>Check-out:</strong> ${checkout}</p>
                    <p><strong>Guests:</strong> ${guests}</p>
                `;
            }, 500);
        }

        // Close modal
        function closeModal() {
            document.getElementById('hotelModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('hotelModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>