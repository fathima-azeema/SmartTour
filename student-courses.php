<?php
// learning.php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['first_name'] ?? 'Student') : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Academy - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3a0ca3;
            --secondary: #7209b7;
            --accent: #f72585;
            --success: #4cc9f0;
            --success-dark: #36b37e;
            --warning: #f8961e;
            --danger: #f94144;
            --dark: #1e293b;
            --light: #f8f9fa;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo i {
            color: var(--primary);
            font-size: 1.8rem;
        }

        .logo span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 60px 0;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            animation: float 15s infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, 20px) rotate(10deg); }
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* Stats Banner */
        .stats-banner {
            background: white;
            border-radius: var(--radius-lg);
            padding: 30px;
            margin-top: -40px;
            margin-bottom: 50px;
            box-shadow: var(--shadow-xl);
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            text-align: center;
        }

        .stat-item {
            transition: var(--transition);
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-top: 8px;
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        /* Section Header */
        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: var(--gray-800);
            margin-bottom: 12px;
        }

        .section-header p {
            color: var(--gray-500);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Course Categories */
        .categories {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .category-btn {
            padding: 12px 28px;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-600);
        }

        .category-btn:hover, .category-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Courses Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .course-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
            position: relative;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .course-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .course-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(67,97,238,0.9), rgba(114,9,183,0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .course-card:hover .course-overlay {
            opacity: 1;
        }

        .preview-btn {
            background: white;
            color: var(--primary);
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .preview-btn:hover {
            transform: scale(1.05);
        }

        .lock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            color: #ffc107;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }

        .free-badge {
            background: rgba(76,201,240,0.9);
            color: white;
        }

        .course-content {
            padding: 20px;
        }

        .course-category {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(67,97,238,0.1);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--gray-800);
        }

        .course-instructor {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .course-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .course-rating {
            color: #ffc107;
            margin-bottom: 16px;
        }

        .course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .course-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .course-price.free {
            color: var(--success-dark);
        }

        .locked-price {
            font-size: 0.9rem;
            color: var(--gray-400);
            text-decoration: line-through;
            margin-right: 5px;
        }

        .enroll-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .enroll-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }

        .enroll-btn.locked {
            background: var(--gray-300);
            cursor: not-allowed;
        }

        .coming-soon {
            position: relative;
        }

        .coming-soon-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, var(--warning), #ff9800);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }

        /* Premium Alert Modal */
        .premium-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .premium-content {
            background: white;
            border-radius: var(--radius-xl);
            max-width: 450px;
            width: 90%;
            padding: 40px;
            text-align: center;
            animation: modalFadeIn 0.3s ease;
        }

        .premium-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent), #ff6b9d);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: white;
        }

        .premium-content h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .premium-content p {
            color: var(--gray-600);
            margin-bottom: 24px;
        }

        .premium-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .premium-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .close-premium {
            margin-top: 16px;
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Newsletter Section */
        .newsletter {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: var(--radius-lg);
            padding: 50px;
            text-align: center;
            color: white;
            margin: 60px 0;
        }

        .newsletter h3 {
            font-size: 1.8rem;
            margin-bottom: 12px;
        }

        .newsletter p {
            margin-bottom: 24px;
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            gap: 12px;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
        }

        .newsletter-btn {
            background: white;
            color: var(--primary);
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Footer */
        .footer {
            background: var(--gray-800);
            color: white;
            padding: 40px 0 20px;
            text-align: center;
        }

        .footer p {
            color: var(--gray-400);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .hero h1 { font-size: 2rem; }
            .stats-banner { grid-template-columns: 1fr; gap: 20px; }
            .courses-grid { grid-template-columns: 1fr; }
            .newsletter-form { flex-direction: column; }
            .categories { gap: 10px; }
            .category-btn { padding: 8px 16px; font-size: 0.8rem; }
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
                    <span>SmartTour</span>
                </a>
                <div>
                    <?php if ($is_logged_in): ?>
                        <span style="color: var(--gray-600);">Welcome, <?php echo htmlspecialchars($user_name); ?> 👋</span>
                    <?php else: ?>
                        <a href="login.php" style="text-decoration: none; color: var(--primary);">Login to unlock premium courses</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>📚 Learning Academy</h1>
            <p>Access tutorials, e-books, and courses to boost your tourism career. Learn from industry experts!</p>
        </div>
    </section>

    <div class="container">
        <!-- Stats Banner -->
        <div class="stats-banner">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-number">100+</div>
                <div class="stat-label">Courses & Tutorials</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                <div class="stat-number">Expert</div>
                <div class="stat-label">Industry Instructors</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                <div class="stat-number">Certified</div>
                <div class="stat-label">Completion Certificates</div>
            </div>
        </div>

        <!-- Section Header -->
        <div class="section-header">
            <h2>🎓 Featured Learning Paths</h2>
            <p>Choose from our curated courses designed to boost your tourism career</p>
        </div>

        <!-- Category Filters -->
        <div class="categories">
            <button class="category-btn active" onclick="filterCourses('all')">All Courses</button>
            <button class="category-btn" onclick="filterCourses('beginner')">Beginner</button>
            <button class="category-btn" onclick="filterCourses('professional')">Professional</button>
            <button class="category-btn" onclick="filterCourses('certification')">Certification</button>
        </div>

        <!-- Courses Grid -->
        <div class="courses-grid" id="coursesGrid">
            <!-- Course 1 - Free Preview Available -->
            <div class="course-card" data-category="beginner">
                <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');">
                    <div class="course-overlay">
                        <button class="preview-btn" onclick="previewCourse('Introduction to Tourism')">
                            <i class="fas fa-play"></i> Watch Preview
                        </button>
                    </div>
                    <div class="free-badge lock-badge">🎬 Free Preview</div>
                </div>
                <div class="course-content">
                    <span class="course-category">📘 Beginner</span>
                    <h3 class="course-title">Introduction to Tourism Industry</h3>
                    <div class="course-instructor">
                        <i class="fas fa-user-circle"></i>
                        <span>Dr. Sarah Miller</span>
                        <span>• 4.8 ★</span>
                    </div>
                    <div class="course-stats">
                        <span><i class="fas fa-clock"></i> 4 hours</span>
                        <span><i class="fas fa-users"></i> 2,345 students</span>
                    </div>
                    <div class="course-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <span style="color: var(--gray-500);"> (4.8)</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price free">FREE</div>
                        <button class="enroll-btn" onclick="startFreeCourse('Introduction to Tourism')">Start Learning →</button>
                    </div>
                </div>
            </div>

            <!-- Course 2 - Premium Locked -->
            <div class="course-card" data-category="professional">
                <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');">
                    <div class="course-overlay">
                        <button class="preview-btn" onclick="showPremiumAlert()">
                            <i class="fas fa-lock"></i> Premium Content
                        </button>
                    </div>
                    <div class="lock-badge">🔒 Premium</div>
                </div>
                <div class="course-content">
                    <span class="course-category">🎯 Professional</span>
                    <h3 class="course-title">Advanced Hospitality Management</h3>
                    <div class="course-instructor">
                        <i class="fas fa-user-circle"></i>
                        <span>Prof. James Wilson</span>
                        <span>• 4.9 ★</span>
                    </div>
                    <div class="course-stats">
                        <span><i class="fas fa-clock"></i> 12 hours</span>
                        <span><i class="fas fa-users"></i> 1,892 students</span>
                    </div>
                    <div class="course-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span style="color: var(--gray-500);"> (4.9)</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price">
                            <span class="locked-price">LKR 15,000</span> Premium
                        </div>
                        <button class="enroll-btn locked" onclick="showPremiumAlert()">🔒 Unlock Premium</button>
                    </div>
                </div>
            </div>

            <!-- Course 3 - Coming Soon -->
            <div class="course-card coming-soon" data-category="certification">
                <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');">
                    <div class="coming-soon-badge">🚀 Coming Soon</div>
                    <div class="course-overlay">
                        <button class="preview-btn" onclick="showComingSoon()">
                            <i class="fas fa-bell"></i> Notify Me
                        </button>
                    </div>
                </div>
                <div class="course-content">
                    <span class="course-category">🎓 Certification</span>
                    <h3 class="course-title">Sustainable Tourism Certification</h3>
                    <div class="course-instructor">
                        <i class="fas fa-user-circle"></i>
                        <span>Dr. Emma Green</span>
                        <span>• Coming Soon</span>
                    </div>
                    <div class="course-stats">
                        <span><i class="fas fa-clock"></i> 20 hours</span>
                        <span><i class="fas fa-certificate"></i> Certificate Included</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price">🚀 Launching Soon</div>
                        <button class="enroll-btn" onclick="showComingSoon()">Get Notified →</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- More Coming Soon Section -->
        <div class="section-header" style="margin-top: 40px;">
            <h2>📖 More Learning Resources Coming Soon</h2>
            <p>We're constantly adding new content to help you grow</p>
        </div>

        <div class="courses-grid">
            <!-- E-Book 1 -->
            <div class="course-card" data-category="beginner">
                <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');">
                    <div class="course-overlay">
                        <button class="preview-btn" onclick="showComingSoon()">
                            <i class="fas fa-download"></i> Coming Soon
                        </button>
                    </div>
                    <div class="lock-badge">📚 E-Book</div>
                </div>
                <div class="course-content">
                    <span class="course-category">📖 E-Book</span>
                    <h3 class="course-title">Sri Lanka Travel Guide 2024</h3>
                    <div class="course-instructor">
                        <i class="fas fa-user-circle"></i>
                        <span>Travel Experts</span>
                    </div>
                    <div class="course-stats">
                        <span><i class="fas fa-file-alt"></i> 250+ pages</span>
                        <span><i class="fas fa-download"></i> PDF Format</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price free">FREE</div>
                        <button class="enroll-btn" onclick="showComingSoon()">Coming Soon →</button>
                    </div>
                </div>
            </div>

            <!-- E-Book 2 -->
            <div class="course-card" data-category="professional">
                <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-1589829085413-56de8ae18c73?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');">
                    <div class="course-overlay">
                        <button class="preview-btn" onclick="showPremiumAlert()">
                            <i class="fas fa-lock"></i> Premium
                        </button>
                    </div>
                    <div class="lock-badge">🔒 Premium</div>
                </div>
                <div class="course-content">
                    <span class="course-category">📘 Guide</span>
                    <h3 class="course-title">Tour Guide Professional Handbook</h3>
                    <div class="course-instructor">
                        <i class="fas fa-user-circle"></i>
                        <span>Senior Guide Association</span>
                    </div>
                    <div class="course-stats">
                        <span><i class="fas fa-file-alt"></i> 180+ pages</span>
                        <span><i class="fas fa-certificate"></i> Certificate Ready</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price">
                            <span class="locked-price">LKR 8,000</span> Premium
                        </div>
                        <button class="enroll-btn locked" onclick="showPremiumAlert()">🔒 Unlock</button>
                    </div>
                </div>
            </div>

            <!-- Tutorial Video -->
            <div class="course-card" data-category="beginner">
                <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');">
                    <div class="course-overlay">
                        <button class="preview-btn" onclick="showComingSoon()">
                            <i class="fas fa-play"></i> Watch Trailer
                        </button>
                    </div>
                    <div class="free-badge lock-badge">🎬 Free</div>
                </div>
                <div class="course-content">
                    <span class="course-category">🎥 Video Tutorial</span>
                    <h3 class="course-title">Mastering Customer Service in Tourism</h3>
                    <div class="course-instructor">
                        <i class="fas fa-user-circle"></i>
                        <span>Maria Gonzales</span>
                    </div>
                    <div class="course-stats">
                        <span><i class="fas fa-clock"></i> 45 mins</span>
                        <span><i class="fas fa-play-circle"></i> 5 Lessons</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price free">FREE</div>
                        <button class="enroll-btn" onclick="showComingSoon()">Watch Now →</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter Section -->
        <div class="newsletter">
            <h3>📧 Get notified about new courses</h3>
            <p>Subscribe to receive updates when new courses and resources are added</p>
            <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                <input type="email" class="newsletter-input" placeholder="Enter your email" required>
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
        </div>
    </div>

    <!-- Premium Alert Modal -->
    <div id="premiumModal" class="premium-modal">
        <div class="premium-content">
            <div class="premium-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h3>✨ Premium Content</h3>
            <p>This course is part of our premium library. Upgrade to unlock all courses, get certificates, and access exclusive content!</p>
            <button class="premium-btn" onclick="upgradeToPremium()">Upgrade to Premium</button>
            <button class="close-premium" onclick="closePremiumModal()">Maybe later</button>
        </div>
    </div>

    <!-- Coming Soon Modal -->
    <div id="comingSoonModal" class="premium-modal">
        <div class="premium-content">
            <div class="premium-icon" style="background: linear-gradient(135deg, var(--warning), #ff9800);">
                <i class="fas fa-rocket"></i>
            </div>
            <h3>🚀 Coming Soon!</h3>
            <p>We're working hard to bring you amazing content. Leave your email and we'll notify you when this course launches!</p>
            <input type="email" id="notifyEmail" placeholder="Your email address" style="width: 100%; padding: 12px; border-radius: 40px; border: 2px solid var(--gray-200); margin-bottom: 16px;">
            <button class="premium-btn" onclick="notifyMe()">Notify Me</button>
            <button class="close-premium" onclick="closeComingSoonModal()">Close</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© 2024 SmartTour Learning Academy. Empowering tourism professionals.</p>
            <p style="margin-top: 8px; font-size: 0.8rem;">🌟 New courses added regularly. Stay tuned!</p>
        </div>
    </footer>

    <script>
        // Course filtering
        function filterCourses(category) {
            const cards = document.querySelectorAll('.course-card');
            const buttons = document.querySelectorAll('.category-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Free course preview
        function startFreeCourse(courseName) {
            alert(`✨ "${courseName}" is now available!\n\nYou can start learning immediately. Check your dashboard for progress tracking.`);
        }

        function previewCourse(courseName) {
            alert(`🎬 Preview: "${courseName}"\n\nWatch the first lesson for free!\n\n(Full course available with premium subscription)`);
        }

        // Premium functions
        function showPremiumAlert() {
            document.getElementById('premiumModal').style.display = 'flex';
        }

        function closePremiumModal() {
            document.getElementById('premiumModal').style.display = 'none';
        }

        function upgradeToPremium() {
            alert(`✨ Premium Subscription\n\n• Unlock all 100+ courses\n• Get verified certificates\n• Access expert webinars\n• Priority support\n\nUpgrade coming soon! 🚀`);
            closePremiumModal();
        }

        // Coming Soon functions
        function showComingSoon() {
            document.getElementById('comingSoonModal').style.display = 'flex';
        }

        function closeComingSoonModal() {
            document.getElementById('comingSoonModal').style.display = 'none';
        }

        function notifyMe() {
            const email = document.getElementById('notifyEmail').value;
            if (email) {
                alert(`📧 Thanks! We'll notify you at ${email} when this course launches.`);
                closeComingSoonModal();
                document.getElementById('notifyEmail').value = '';
            } else {
                alert('Please enter your email address.');
            }
        }

        // Newsletter subscription
        function subscribeNewsletter(event) {
            event.preventDefault();
            const email = event.target.querySelector('input').value;
            alert(`📧 Thanks for subscribing! You'll receive updates about new courses at ${email}`);
            event.target.reset();
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const premiumModal = document.getElementById('premiumModal');
            const comingSoonModal = document.getElementById('comingSoonModal');
            if (event.target == premiumModal) closePremiumModal();
            if (event.target == comingSoonModal) closeComingSoonModal();
        }

        // Animate stats on scroll
        const observerOptions = { threshold: 0.5 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const numbers = entry.target.querySelectorAll('.stat-number');
                    numbers.forEach(num => {
                        const target = num.innerText;
                        num.style.opacity = '1';
                    });
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stats-banner').forEach(el => observer.observe(el));
    </script>
</body>
</html>