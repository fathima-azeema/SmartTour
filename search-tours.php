<?php
// search-tours.php
require_once 'config.php';
require_once 'session-check.php';

// Get database connection
$conn = getDBConnection();

// Pagination settings
$tours_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tours_per_page;

// Build search query based on filters
$where_clauses = array();
$params = array();
$types = "";

// Location filter
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
if (!empty($location)) {
    $where_clauses[] = "(tour_name LIKE ? OR location LIKE ? OR description LIKE ?)";
    $search_term = "%$location%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Category filter
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
if (!empty($category) && $category != 'all') {
    $where_clauses[] = "category = ?";
    $params[] = $category;
    $types .= "s";
}

// Duration filter
$duration = isset($_GET['duration']) ? $_GET['duration'] : '';
if (!empty($duration)) {
    switch($duration) {
        case '1-3':
            $where_clauses[] = "duration_days BETWEEN 1 AND 3";
            break;
        case '4-7':
            $where_clauses[] = "duration_days BETWEEN 4 AND 7";
            break;
        case '8-14':
            $where_clauses[] = "duration_days BETWEEN 8 AND 14";
            break;
        case '15+':
            $where_clauses[] = "duration_days >= 15";
            break;
    }
}

// Price range filter
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 100000;
if ($min_price > 0 || $max_price < 100000) {
    $where_clauses[] = "price_per_person BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types .= "dd";
}

// Group size filter
$max_participants = isset($_GET['max_participants']) ? (int)$_GET['max_participants'] : 0;
if ($max_participants > 0) {
    $where_clauses[] = "max_participants <= ?";
    $params[] = $max_participants;
    $types .= "i";
}

// Difficulty level
$difficulty = isset($_GET['difficulty']) ? sanitize($_GET['difficulty']) : '';
if (!empty($difficulty) && $difficulty != 'all') {
    $where_clauses[] = "difficulty = ?";
    $params[] = $difficulty;
    $types .= "s";
}

// Build WHERE clause
$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM tours $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_tours = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_tours / $tours_per_page);

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$order_by = "ORDER BY ";
switch ($sort) {
    case 'price_low':
        $order_by .= "price_per_person ASC";
        break;
    case 'price_high':
        $order_by .= "price_per_person DESC";
        break;
    case 'duration':
        $order_by .= "duration_days ASC";
        break;
    case 'rating':
        $order_by .= "rating DESC";
        break;
    case 'name':
        $order_by .= "tour_name ASC";
        break;
    default:
        $order_by .= "id DESC"; // popular/newest
}

// Get tours for current page
$sql = "SELECT * FROM tours $where_sql $order_by LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    // Add pagination params
    $all_params = array_merge($params, [$tours_per_page, $offset]);
    $types .= "ii";
    $stmt->bind_param($types, ...$all_params);
} else {
    $stmt->bind_param("ii", $tours_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

$tours = array();
while ($row = $result->fetch_assoc()) {
    $tours[] = $row;
}

$stmt->close();
$count_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Tours - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="css/footer.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #27ae60; /* Green for tours */
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #e8f5e9; /* Light green */
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
        }

        .signup-btn:hover {
            background: #219653;
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
            background: #219653;
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

        /* Category Tags */
        .category-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .category-tag {
            padding: 8px 15px;
            background: var(--light);
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-tag:hover,
        .category-tag.active {
            background: var(--primary);
            color: white;
        }

        /* Difficulty Level */
        .difficulty-levels {
            display: flex;
            gap: 10px;
        }

        .difficulty-option {
            flex: 1;
            text-align: center;
            padding: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .difficulty-option:hover,
        .difficulty-option.active {
            border-color: var(--primary);
            background: rgba(39, 174, 96, 0.1);
        }

        .difficulty-option.easy { color: #27ae60; }
        .difficulty-option.moderate { color: #f39c12; }
        .difficulty-option.challenging { color: #e74c3c; }

        /* Price Range */
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

        /* Duration Slider */
        .duration-slider {
            margin-top: 15px;
        }

        .duration-slider input[type="range"] {
            width: 100%;
        }

        .duration-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #666;
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

        /* Tour Cards Grid */
        .tours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .tour-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .tour-card:hover {
            transform: translateY(-5px);
        }

        .tour-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .tour-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .tour-card:hover .tour-image img {
            transform: scale(1.1);
        }

        .tour-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .tour-category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .tour-content {
            padding: 20px;
        }

        .tour-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .tour-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary);
        }

        .tour-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .tour-price span {
            font-size: 0.8rem;
            font-weight: normal;
            color: #999;
        }

        .tour-location {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .tour-location i {
            color: var(--primary);
        }

        .tour-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--primary);
        }

        .tour-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 15px;
        }

        .stars {
            color: #ffc107;
        }

        .rating-text {
            color: #666;
            font-size: 0.9rem;
        }

        .tour-highlights {
            margin-bottom: 15px;
        }

        .highlight-tag {
            display: inline-block;
            background: var(--light);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .tour-actions {
            display: flex;
            gap: 10px;
        }

        .view-btn {
            flex: 1;
            padding: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-btn:hover {
            background: #219653;
        }

        .wishlist-btn {
            padding: 10px 15px;
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .wishlist-btn:hover {
            background: var(--accent);
            color: white;
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

        /* No Results */
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

        .no-results p {
            color: #666;
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
            
            .tours-grid {
                grid-template-columns: 1fr;
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
                    <a href="search-restaurants.php">Restaurants</a>
                    <a href="search-tours.php" class="active">Tours</a>
                    <a href="about-us.php">About</a>
                    <a href="contact.php">Contact</a>
                </nav>
                
                <div class="user-menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="login-btn">Dashboard</a>
                    <?php else: ?>
                        <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
                        <button class="signup-btn" onclick="window.location.href='signup.php'">Sign Up</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Search Section -->
    <section class="search-hero">
        <div class="container">
            <h1>Discover Amazing Tours in Sri Lanka</h1>
            <p>From cultural adventures to wildlife safaris - find your perfect tour experience</p>
            
            <!-- Search Form -->
            <div class="search-form-container">
                <form method="GET" action="search-tours.php" id="searchForm">
                    <div class="search-row">
                        <div class="search-group">
                            <label><i class="fas fa-map-marker-alt"></i> Destination</label>
                            <input type="text" name="location" placeholder="Where do you want to go?" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-tag"></i> Category</label>
                            <select name="category">
                                <option value="all">All Categories</option>
                                <option value="cultural">Cultural</option>
                                <option value="adventure">Adventure</option>
                                <option value="wildlife">Wildlife</option>
                                <option value="beach">Beach</option>
                                <option value="religious">Religious</option>
                                <option value="hiking">Hiking</option>
                            </select>
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-calendar"></i> Duration</label>
                            <select name="duration">
                                <option value="">Any Duration</option>
                                <option value="1-3">1-3 Days</option>
                                <option value="4-7">4-7 Days</option>
                                <option value="8-14">8-14 Days</option>
                                <option value="15+">15+ Days</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Find Tours
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
                    <form method="GET" action="search-tours.php" id="filterForm">
                        <!-- Category Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-tag"></i> Tour Category
                            </h3>
                            <div class="category-tags">
                                <span class="category-tag <?php echo $category == 'cultural' ? 'active' : ''; ?>" onclick="selectCategory('cultural')">🏛️ Cultural</span>
                                <span class="category-tag <?php echo $category == 'adventure' ? 'active' : ''; ?>" onclick="selectCategory('adventure')">⛰️ Adventure</span>
                                <span class="category-tag <?php echo $category == 'wildlife' ? 'active' : ''; ?>" onclick="selectCategory('wildlife')">🦁 Wildlife</span>
                                <span class="category-tag <?php echo $category == 'beach' ? 'active' : ''; ?>" onclick="selectCategory('beach')">🏖️ Beach</span>
                                <span class="category-tag <?php echo $category == 'religious' ? 'active' : ''; ?>" onclick="selectCategory('religious')">🕉️ Religious</span>
                                <span class="category-tag <?php echo $category == 'hiking' ? 'active' : ''; ?>" onclick="selectCategory('hiking')">🥾 Hiking</span>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-rupee-sign"></i> Price Range (per person)
                            </h3>
                            <div class="price-range">
                                <div class="price-inputs">
                                    <input type="number" name="min_price" class="price-input" placeholder="Min" value="<?php echo $min_price ?: ''; ?>">
                                    <input type="number" name="max_price" class="price-input" placeholder="Max" value="<?php echo $max_price != 100000 ? $max_price : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Duration Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-clock"></i> Duration
                            </h3>
                            <div class="filter-options">
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="duration" value="1-3" <?php echo $duration == '1-3' ? 'checked' : ''; ?>>
                                        <span>1-3 Days</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="duration" value="4-7" <?php echo $duration == '4-7' ? 'checked' : ''; ?>>
                                        <span>4-7 Days</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="duration" value="8-14" <?php echo $duration == '8-14' ? 'checked' : ''; ?>>
                                        <span>8-14 Days</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="duration" value="15+" <?php echo $duration == '15+' ? 'checked' : ''; ?>>
                                        <span>15+ Days</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="duration" value="" <?php echo empty($duration) ? 'checked' : ''; ?>>
                                        <span>Any Duration</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Difficulty Level -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-mountain"></i> Difficulty
                            </h3>
                            <div class="difficulty-levels">
                                <div class="difficulty-option easy <?php echo $difficulty == 'easy' ? 'active' : ''; ?>" onclick="selectDifficulty('easy')">
                                    Easy
                                </div>
                                <div class="difficulty-option moderate <?php echo $difficulty == 'moderate' ? 'active' : ''; ?>" onclick="selectDifficulty('moderate')">
                                    Moderate
                                </div>
                                <div class="difficulty-option challenging <?php echo $difficulty == 'challenging' ? 'active' : ''; ?>" onclick="selectDifficulty('challenging')">
                                    Challenging
                                </div>
                            </div>
                        </div>

                        <!-- Group Size -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-users"></i> Group Size
                            </h3>
                            <div class="filter-options">
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="max_participants" value="5" <?php echo $max_participants == 5 ? 'checked' : ''; ?>>
                                        <span>Small Group (≤5)</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="max_participants" value="10" <?php echo $max_participants == 10 ? 'checked' : ''; ?>>
                                        <span>Medium Group (≤10)</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="max_participants" value="20" <?php echo $max_participants == 20 ? 'checked' : ''; ?>>
                                        <span>Large Group (≤20)</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="max_participants" value="0" <?php echo $max_participants == 0 ? 'checked' : ''; ?>>
                                        <span>Any Size</span>
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
                            <strong><?php echo $total_tours; ?></strong> tours found
                        </div>
                        <div>
                            <select class="sort-select" name="sort" form="searchForm" onchange="this.form.submit()">
                                <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                                <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="duration" <?php echo $sort == 'duration' ? 'selected' : ''; ?>>Duration</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tours Grid -->
                    <?php if (empty($tours)): ?>
                        <div class="no-results">
                            <i class="fas fa-map-marked-alt"></i>
                            <h3>No Tours Found</h3>
                            <p>Try adjusting your filters or search criteria</p>
                        </div>
                    <?php else: ?>
                        <div class="tours-grid">
                            <?php foreach ($tours as $tour): ?>
                            <div class="tour-card">
                                <div class="tour-image">
                                    <img src="<?php echo $tour['image_url'] ?: 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'; ?>" 
                                         alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                                    <div class="tour-category-badge">
                                        <?php echo ucfirst($tour['category']); ?>
                                    </div>
                                    <?php if ($tour['rating'] >= 4.5): ?>
                                    <div class="tour-badge">Top Rated</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="tour-content">
                                    <div class="tour-header">
                                        <h3 class="tour-name"><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                                        <div class="tour-price">
                                            LKR <?php echo number_format($tour['price_per_person']); ?>
                                            <span>/person</span>
                                        </div>
                                    </div>
                                    
                                    <div class="tour-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($tour['location']); ?>
                                    </div>
                                    
                                    <div class="tour-meta">
                                        <div class="meta-item">
                                            <i class="far fa-clock"></i>
                                            <span><?php echo $tour['duration_days']; ?> days</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-users"></i>
                                            <span>Max <?php echo $tour['max_participants']; ?></span>
                                        </div>
                                        <?php if (isset($tour['difficulty'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-mountain"></i>
                                            <span><?php echo ucfirst($tour['difficulty']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="tour-rating">
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
                                        <span class="rating-text"><?php echo number_format($tour['rating'] ?? 4.5, 1); ?> (120+ reviews)</span>
                                    </div>
                                    
                                    <div class="tour-highlights">
                                        <?php if (isset($tour['highlights'])): ?>
                                            <?php 
                                            $highlights = explode(',', $tour['highlights']);
                                            foreach(array_slice($highlights, 0, 3) as $highlight): ?>
                                                <span class="highlight-tag"><?php echo trim($highlight); ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="highlight-tag">Expert Guide</span>
                                            <span class="highlight-tag">Free Pickup</span>
                                            <span class="highlight-tag">Lunch Included</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="tour-actions">
                                        <button class="view-btn" onclick="viewTour(<?php echo $tour['id']; ?>)">
                                            View Details
                                        </button>
                                        <button class="wishlist-btn" onclick="addToWishlist(<?php echo $tour['id']; ?>)">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&location=<?php echo urlencode($location); ?>&category=<?php echo urlencode($category); ?>&duration=<?php echo urlencode($duration); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&difficulty=<?php echo urlencode($difficulty); ?>&sort=<?php echo $sort; ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&location=<?php echo urlencode($location); ?>&category=<?php echo urlencode($category); ?>&duration=<?php echo urlencode($duration); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&difficulty=<?php echo urlencode($difficulty); ?>&sort=<?php echo $sort; ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?>&location=<?php echo urlencode($location); ?>&category=<?php echo urlencode($category); ?>&duration=<?php echo urlencode($duration); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&difficulty=<?php echo urlencode($difficulty); ?>&sort=<?php echo $sort; ?>" class="page-link">
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

    <script>
        // Category selection
        function selectCategory(category) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'category';
            input.value = category;
            
            const form = document.getElementById('filterForm');
            form.appendChild(input);
            form.submit();
        }

        // Difficulty selection
        function selectDifficulty(difficulty) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'difficulty';
            input.value = difficulty;
            
            const form = document.getElementById('filterForm');
            form.appendChild(input);
            form.submit();
        }

        // View tour details
        function viewTour(tourId) {
            window.location.href = 'tour-booking.php?tour_id=' + tourId;
        }

        // Add to wishlist
        function addToWishlist(tourId) {
            <?php if (isset($_SESSION['user_id'])): ?>
                alert('Tour added to wishlist!');
            <?php else: ?>
                if (confirm('Please login to save tours to your wishlist')) {
                    window.location.href = 'login.php?redirect=search-tours.php';
                }
            <?php endif; ?>
        }
    </script>
</body>
</html>