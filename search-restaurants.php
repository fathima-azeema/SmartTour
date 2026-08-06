<?php
// search-restaurants.php
require_once 'config.php';
require_once 'session-check.php';

// Get database connection
$conn = getDBConnection();

// Pagination settings
$restaurants_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $restaurants_per_page;

// Build search query based on filters
$where_clauses = array();
$params = array();
$types = "";

// Location filter
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
if (!empty($location)) {
    $where_clauses[] = "(restaurant_name LIKE ? OR location LIKE ? OR address LIKE ?)";
    $search_term = "%$location%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Cuisine filter
$cuisine = isset($_GET['cuisine']) ? sanitize($_GET['cuisine']) : '';
if (!empty($cuisine) && $cuisine != 'all') {
    $where_clauses[] = "cuisine_type LIKE ?";
    $params[] = "%$cuisine%";
    $types .= "s";
}

// Price range filter (1=$ 2=$$ 3=$$$)
$price_level = isset($_GET['price_level']) ? (int)$_GET['price_level'] : 0;
if ($price_level > 0) {
    $where_clauses[] = "price_level = ?";
    $params[] = $price_level;
    $types .= "i";
}

// Rating filter
$min_rating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : 0;
if ($min_rating > 0) {
    $where_clauses[] = "rating >= ?";
    $params[] = $min_rating;
    $types .= "d";
}

// Dietary options
$dietary = isset($_GET['dietary']) ? $_GET['dietary'] : array();
if (!empty($dietary)) {
    foreach ($dietary as $option) {
        $where_clauses[] = "dietary_options LIKE ?";
        $params[] = "%$option%";
        $types .= "s";
    }
}

// Build WHERE clause
$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM restaurants $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_restaurants = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_restaurants / $restaurants_per_page);

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$order_by = "ORDER BY ";
switch ($sort) {
    case 'rating':
        $order_by .= "rating DESC";
        break;
    case 'price_low':
        $order_by .= "price_level ASC";
        break;
    case 'price_high':
        $order_by .= "price_level DESC";
        break;
    case 'name':
        $order_by .= "restaurant_name ASC";
        break;
    default:
        $order_by .= "id DESC";
}

// Get restaurants for current page
$sql = "SELECT * FROM restaurants $where_sql $order_by LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $all_params = array_merge($params, [$restaurants_per_page, $offset]);
    $types .= "ii";
    $stmt->bind_param($types, ...$all_params);
} else {
    $stmt->bind_param("ii", $restaurants_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

$restaurants = array();
while ($row = $result->fetch_assoc()) {
    $restaurants[] = $row;
}

$stmt->close();
$count_stmt->close();
$conn->close();

// Helper function to get price display
function getPriceDisplay($price_level) {
    if ($price_level == 1) {
        return '$ (Budget)';
    } elseif ($price_level == 2) {
        return '$$ (Moderate)';
    } elseif ($price_level >= 3) {
        return '$$$ (Premium)';
    }
    return '$$ (Moderate)';
}

// Helper function to get price symbols
function getPriceSymbols($price_level) {
    $symbols = '';
    if ($price_level == 1) {
        $symbols = '$';
    } elseif ($price_level == 2) {
        $symbols = '$$';
    } elseif ($price_level >= 3) {
        $symbols = '$$$';
    } else {
        $symbols = '$$';
    }
    return $symbols;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Restaurants - SmartTour</title>
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
            --primary: #e67e22;
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
            background: #d35400;
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
            font-family: 'Poppins', sans-serif;
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
            font-family: 'Poppins', sans-serif;
        }

        .search-btn:hover {
            background: #d35400;
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

        /* Cuisine Tags */
        .cuisine-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .cuisine-tag {
            padding: 6px 12px;
            background: var(--light);
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cuisine-tag:hover,
        .cuisine-tag.active {
            background: var(--primary);
            color: white;
        }

        /* Price Level */
        .price-level {
            display: flex;
            gap: 10px;
        }

        .price-option {
            flex: 1;
            text-align: center;
            padding: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .price-option:hover,
        .price-option.active {
            border-color: var(--primary);
            background: rgba(230, 126, 34, 0.1);
        }

        .price-option span {
            font-weight: 600;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .price-option div {
            font-size: 0.7rem;
            color: var(--text);
        }

        /* Rating Filter */
        .rating-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stars {
            color: #ffc107;
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
            font-family: 'Poppins', sans-serif;
        }

        /* Restaurant Cards Grid */
        .restaurants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .restaurant-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
        }

        .restaurant-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .restaurant-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .restaurant-card:hover .restaurant-image img {
            transform: scale(1.1);
        }

        .restaurant-badge {
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

        .restaurant-content {
            padding: 20px;
        }

        .restaurant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .restaurant-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary);
        }

        .restaurant-price {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            background: var(--light);
            padding: 3px 10px;
            border-radius: 20px;
        }

        .restaurant-location {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .restaurant-location i {
            color: var(--primary);
        }

        .restaurant-rating {
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

        .cuisine-type {
            display: inline-block;
            background: var(--light);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }

        .restaurant-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .feature-tag {
            background: var(--light);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .feature-tag i {
            color: var(--primary);
            font-size: 0.7rem;
        }

        .restaurant-actions {
            display: flex;
            gap: 10px;
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
            font-family: 'Poppins', sans-serif;
        }

        .view-btn:hover {
            background: #d35400;
        }

        .wishlist-btn {
            padding: 10px 15px;
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 8px;
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
            font-family: 'Poppins', sans-serif;
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
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
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
            
            .restaurants-grid {
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
                    <i class="fas fa-utensils"></i>
                    <span class="logo-text">SmartTour</span>
                </a>
                
                <nav class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="search-hotels.php">Hotels</a>
                    <a href="search-restaurants.php" class="active">Restaurants</a>
                    <a href="search-tours.php">Tours</a>
                    <a href="about-us.php">About</a>
                    <a href="contact.php">Contact</a>
                </nav>
                
                <div class="user-menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="login-btn">Dashboard</a>
                    <?php else: ?>
                        <a href="login.php" class="login-btn">Login</a>
                        <a href="signup.php" class="signup-btn">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Search Section -->
    <section class="search-hero">
        <div class="container">
            <h1>Discover Sri Lanka's Best Restaurants</h1>
            <p>From street food to fine dining - find the perfect place to eat</p>
            
            <!-- Search Form -->
            <div class="search-form-container">
                <form method="GET" action="search-restaurants.php" id="searchForm">
                    <div class="search-row">
                        <div class="search-group">
                            <label><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" name="location" placeholder="Where do you want to eat?" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-utensils"></i> Cuisine</label>
                            <select name="cuisine">
                                <option value="all" <?php echo $cuisine == 'all' ? 'selected' : ''; ?>>All Cuisines</option>
                                <option value="sri lankan" <?php echo $cuisine == 'sri lankan' ? 'selected' : ''; ?>>Sri Lankan</option>
                                <option value="indian" <?php echo $cuisine == 'indian' ? 'selected' : ''; ?>>Indian</option>
                                <option value="chinese" <?php echo $cuisine == 'chinese' ? 'selected' : ''; ?>>Chinese</option>
                                <option value="italian" <?php echo $cuisine == 'italian' ? 'selected' : ''; ?>>Italian</option>
                                <option value="seafood" <?php echo $cuisine == 'seafood' ? 'selected' : ''; ?>>Seafood</option>
                                <option value="international" <?php echo $cuisine == 'international' ? 'selected' : ''; ?>>International</option>
                            </select>
                        </div>
                        
                        <div class="search-group">
                            <label><i class="fas fa-users"></i> Guests</label>
                            <select name="guests">
                                <option value="1">1 Person</option>
                                <option value="2" selected>2 People</option>
                                <option value="3">3 People</option>
                                <option value="4">4 People</option>
                                <option value="5">5+ People</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Find Restaurants
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
                    <form method="GET" action="search-restaurants.php" id="filterForm">
                        <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                        
                        <!-- Price Level Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-rupee-sign"></i> Price Range
                            </h3>
                            <div class="price-level">
                                <div class="price-option <?php echo $price_level == 1 ? 'active' : ''; ?>" onclick="selectPrice(1)">
                                    <span>$</span>
                                    <div>Budget</div>
                                </div>
                                <div class="price-option <?php echo $price_level == 2 ? 'active' : ''; ?>" onclick="selectPrice(2)">
                                    <span>$$</span>
                                    <div>Moderate</div>
                                </div>
                                <div class="price-option <?php echo $price_level == 3 ? 'active' : ''; ?>" onclick="selectPrice(3)">
                                    <span>$$$</span>
                                    <div>Premium</div>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-star"></i> Minimum Rating
                            </h3>
                            <div class="filter-options">
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="min_rating" value="4.5" <?php echo $min_rating == 4.5 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <span class="rating-option">
                                            <span class="stars">★★★★★</span> 4.5+
                                        </span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="min_rating" value="4.0" <?php echo $min_rating == 4.0 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <span class="rating-option">
                                            <span class="stars">★★★★☆</span> 4.0+
                                        </span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="min_rating" value="3.5" <?php echo $min_rating == 3.5 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <span class="rating-option">
                                            <span class="stars">★★★☆☆</span> 3.5+
                                        </span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="radio" name="min_rating" value="0" <?php echo $min_rating == 0 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <span>All Ratings</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Dietary Options -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                <i class="fas fa-leaf"></i> Dietary Options
                            </h3>
                            <div class="filter-options">
                                <div class="filter-option">
                                    <label>
                                        <input type="checkbox" name="dietary[]" value="vegetarian" onchange="this.form.submit()">
                                        <span>🥬 Vegetarian Friendly</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="checkbox" name="dietary[]" value="vegan" onchange="this.form.submit()">
                                        <span>🌱 Vegan Options</span>
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <label>
                                        <input type="checkbox" name="dietary[]" value="halal" onchange="this.form.submit()">
                                        <span>☪️ Halal</span>
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
                            <strong><?php echo $total_restaurants; ?></strong> restaurants found
                        </div>
                        <div>
                            <select class="sort-select" name="sort" form="searchForm" onchange="this.form.submit()">
                                <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                                <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                            </select>
                        </div>
                    </div>

                    <!-- Restaurants Grid -->
                    <?php if (empty($restaurants)): ?>
                        <div class="no-results">
                            <i class="fas fa-utensils"></i>
                            <h3>No Restaurants Found</h3>
                            <p>Try adjusting your filters or search criteria</p>
                        </div>
                    <?php else: ?>
                        <div class="restaurants-grid">
                            <?php foreach ($restaurants as $restaurant): ?>
                            <div class="restaurant-card">
                                <div class="restaurant-image">
                                    <img src="<?php echo $restaurant['image_url'] ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'; ?>" 
                                         alt="<?php echo htmlspecialchars($restaurant['restaurant_name']); ?>">
                                    <?php if ($restaurant['rating'] >= 4.5): ?>
                                    <div class="restaurant-badge">Top Rated</div>
                                    <?php endif; ?>
                                </div>
                                <div class="restaurant-content">
                                    <div class="restaurant-header">
                                        <h3 class="restaurant-name"><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></h3>
                                        <div class="restaurant-price">
                                            <?php echo getPriceSymbols($restaurant['price_level']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="restaurant-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($restaurant['location']); ?>
                                    </div>
                                    
                                    <div class="restaurant-rating">
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
                                        <span class="rating-text"><?php echo number_format($restaurant['rating'], 1); ?> (<?php echo rand(50, 500); ?> reviews)</span>
                                    </div>
                                    
                                    <span class="cuisine-type"><?php echo htmlspecialchars($restaurant['cuisine_type']); ?></span>
                                    
                                    <div class="restaurant-features">
                                        <?php if ($restaurant['has_wifi']): ?>
                                        <span class="feature-tag"><i class="fas fa-wifi"></i> WiFi</span>
                                        <?php endif; ?>
                                        <?php if ($restaurant['has_parking']): ?>
                                        <span class="feature-tag"><i class="fas fa-parking"></i> Parking</span>
                                        <?php endif; ?>
                                        <?php if ($restaurant['has_ac']): ?>
                                        <span class="feature-tag"><i class="fas fa-snowflake"></i> AC</span>
                                        <?php endif; ?>
                                        <span class="feature-tag"><i class="fas fa-clock"></i> <?php echo $restaurant['opening_hours']; ?></span>
                                    </div>
                                    
                                    <div class="restaurant-actions">
                                        <button class="view-btn" onclick="viewRestaurant(<?php echo $restaurant['id']; ?>, '<?php echo htmlspecialchars($restaurant['restaurant_name']); ?>')">
                                            View Details
                                        </button>
                                        <button class="wishlist-btn" onclick="addToWishlist(<?php echo $restaurant['id']; ?>)">
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
                            <a href="?page=<?php echo $page-1; ?>&location=<?php echo urlencode($location); ?>&cuisine=<?php echo urlencode($cuisine); ?>&price_level=<?php echo $price_level; ?>&min_rating=<?php echo $min_rating; ?>&sort=<?php echo $sort; ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&location=<?php echo urlencode($location); ?>&cuisine=<?php echo urlencode($cuisine); ?>&price_level=<?php echo $price_level; ?>&min_rating=<?php echo $min_rating; ?>&sort=<?php echo $sort; ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?>&location=<?php echo urlencode($location); ?>&cuisine=<?php echo urlencode($cuisine); ?>&price_level=<?php echo $price_level; ?>&min_rating=<?php echo $min_rating; ?>&sort=<?php echo $sort; ?>" class="page-link">
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
    <div id="restaurantModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Restaurant Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content loaded via JavaScript -->
            </div>
            <div class="modal-footer">
                <button class="login-btn" onclick="closeModal()">Close</button>
                <button class="view-btn" id="bookTableBtn">Book a Table</button>
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

    <script>
        // Price selection
        function selectPrice(level) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'price_level';
            input.value = level;
            
            const form = document.getElementById('filterForm');
            form.appendChild(input);
            form.submit();
        }
        
        // View restaurant details
        let currentRestaurantId = null;
        
        function viewRestaurant(restaurantId, restaurantName) {
            currentRestaurantId = restaurantId;
            const modal = document.getElementById('restaurantModal');
            const modalBody = document.getElementById('modalBody');
            const bookBtn = document.getElementById('bookTableBtn');
            
            bookBtn.onclick = function() {
                window.location.href = `restaurant-booking.php?restaurant_id=${restaurantId}&guests=2&date=<?php echo date('Y-m-d'); ?>&time=19:00`;
            };
            
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                    <p style="margin-top: 15px;">Loading restaurant details...</p>
                </div>
            `;
            modal.style.display = 'flex';
            
            setTimeout(() => {
                modalBody.innerHTML = `
                    <h3>${restaurantName}</h3>
                    <hr>
                    <p><strong>📍 Location:</strong> Colombo, Sri Lanka</p>
                    <p><strong>🍽️ Cuisine:</strong> Sri Lankan & Seafood</p>
                    <p><strong>⏰ Opening Hours:</strong> 11:00 AM - 11:00 PM</p>
                    <p><strong>💰 Price Range:</strong> $ (Budget Friendly)</p>
                    <p><strong>⭐ Rating:</strong> 4.5/5 (230 reviews)</p>
                    <hr>
                    <h4>Popular Dishes:</h4>
                    <ul>
                        <li>✔️ Kottu Roti</li>
                        <li>✔️ Sri Lankan Rice & Curry</li>
                        <li>✔️ Deviled Prawns</li>
                        <li>✔️ Hoppers</li>
                    </ul>
                    <p style="margin-top: 15px;">Click "Book a Table" to make a reservation at this restaurant.</p>
                `;
            }, 500);
        }
        
        // Add to wishlist
        function addToWishlist(restaurantId) {
            <?php if (isset($_SESSION['user_id'])): ?>
                alert('Restaurant added to your wishlist!');
            <?php else: ?>
                if (confirm('Please login to save restaurants to your wishlist')) {
                    window.location.href = 'login.php?redirect=search-restaurants.php';
                }
            <?php endif; ?>
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('restaurantModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('restaurantModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Auto-submit rating filters
        document.querySelectorAll('input[name="min_rating"]').forEach(input => {
            input.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>