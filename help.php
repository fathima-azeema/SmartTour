<?php
// help.php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['first_name'] ?? 'User') : 'Guest';
$user_type = $is_logged_in ? ($_SESSION['user_type'] ?? 'tourist') : 'guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support Center - SmartTour</title>
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

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="%234361ee" fill-opacity="0.03" d="M100 10L110 40L140 40L115 60L125 90L100 70L75 90L85 60L60 40L90 40L100 10Z"/><circle cx="50" cy="150" r="8" fill="%237209b7" fill-opacity="0.03"/><circle cx="160" cy="130" r="12" fill="%234cc9f0" fill-opacity="0.03"/></svg>');
            background-repeat: repeat;
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 2;
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

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 20px); }
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

        /* Search Bar */
        .search-container {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-top: -30px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-xl);
            position: relative;
            z-index: 10;
        }

        .search-box {
            display: flex;
            gap: 12px;
            align-items: center;
            background: var(--gray-100);
            border-radius: 60px;
            padding: 5px;
        }

        .search-box i {
            position: absolute;
            left: 25px;
            color: var(--gray-400);
        }

        .search-input {
            flex: 1;
            padding: 16px 20px 16px 50px;
            border: none;
            background: transparent;
            font-size: 1rem;
            outline: none;
        }

        .search-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }

        /* Quick Links */
        .quick-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .quick-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: white;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--gray-700);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .quick-link:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            color: var(--primary);
        }

        .quick-link i {
            font-size: 1.8rem;
            color: var(--primary);
        }

        /* Category Tabs */
        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 12px;
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

        /* FAQ Section */
        .faq-section {
            margin-bottom: 60px;
        }

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

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
            gap: 20px;
        }

        .faq-item {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .faq-item:hover {
            box-shadow: var(--shadow-md);
        }

        .faq-question {
            padding: 20px 24px;
            font-weight: 600;
            font-size: 1rem;
            color: var(--gray-800);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .faq-question:hover {
            background: rgba(67,97,238,0.05);
        }

        .faq-question i {
            color: var(--primary);
            transition: transform var(--transition);
        }

        .faq-answer {
            padding: 0 24px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            color: var(--gray-600);
            line-height: 1.7;
            border-top: 1px solid transparent;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-item.active .faq-answer {
            padding: 0 24px 20px 24px;
            max-height: 300px;
            border-top-color: var(--gray-200);
        }

        /* Contact Support Section */
        .contact-support {
            background: white;
            border-radius: var(--radius-xl);
            padding: 50px;
            margin: 40px 0;
            text-align: center;
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, white, var(--gray-50));
        }

        .contact-support h3 {
            font-size: 1.8rem;
            margin-bottom: 16px;
            color: var(--gray-800);
        }

        .contact-support p {
            color: var(--gray-600);
            margin-bottom: 30px;
        }

        .contact-methods {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .contact-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px 30px;
            background: white;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--gray-700);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .contact-card i {
            font-size: 2rem;
            color: var(--primary);
        }

        .contact-card .info h4 {
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .contact-card .info p {
            margin: 0;
            font-size: 0.85rem;
        }

        /* Ticket Form */
        .ticket-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-xl);
            padding: 50px;
            margin: 40px 0;
            color: white;
        }

        .ticket-section h3 {
            font-size: 1.8rem;
            margin-bottom: 16px;
            text-align: center;
        }

        .ticket-section p {
            text-align: center;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .ticket-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-ticket {
            width: 100%;
            padding: 14px;
            background: white;
            color: var(--primary);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Footer */
        .footer {
            background: var(--gray-800);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
            text-align: center;
        }

        .footer p {
            color: var(--gray-400);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: var(--radius-xl);
            max-width: 450px;
            width: 90%;
            padding: 40px;
            text-align: center;
            animation: modalFadeIn 0.3s ease;
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

        .modal-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--success), var(--success-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: white;
        }

        .modal h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .modal p {
            color: var(--gray-600);
            margin-bottom: 24px;
        }

        .modal-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
        }

        .close-modal {
            margin-top: 16px;
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .hero h1 { font-size: 2rem; }
            .faq-grid { grid-template-columns: 1fr; }
            .contact-methods { flex-direction: column; align-items: stretch; }
            .contact-card { justify-content: center; }
            .search-box { flex-direction: column; background: transparent; }
            .search-input { background: var(--gray-100); border-radius: 50px; }
            .search-btn { width: 100%; }
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
                        <a href="login.php" style="text-decoration: none; color: var(--primary);">Login for Support</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>💬 How Can We Help You?</h1>
            <p>Find answers, get support, and connect with our team. We're here for you 24/7!</p>
        </div>
    </section>

    <div class="container">
        <!-- Search Bar -->
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="Search for answers, guides, or topics...">
                <button class="search-btn" onclick="searchFAQ()">Search</button>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <a href="#" class="quick-link" onclick="filterByCategory('all')">
                <i class="fas fa-question-circle"></i>
                <span>All Topics</span>
            </a>
            <a href="#" class="quick-link" onclick="filterByCategory('tourist')">
                <i class="fas fa-umbrella-beach"></i>
                <span>For Tourists</span>
            </a>
            <a href="#" class="quick-link" onclick="filterByCategory('student')">
                <i class="fas fa-graduation-cap"></i>
                <span>For Students</span>
            </a>
            <a href="#" class="quick-link" onclick="filterByCategory('guide')">
                <i class="fas fa-map-signs"></i>
                <span>For Guides</span>
            </a>
            <a href="#" class="quick-link" onclick="filterByCategory('technical')">
                <i class="fas fa-laptop-code"></i>
                <span>Technical Help</span>
            </a>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <button class="category-btn active" data-category="all" onclick="filterFAQs('all')">📚 All FAQs</button>
            <button class="category-btn" data-category="tourist" onclick="filterFAQs('tourist')">🏖️ Tourists</button>
            <button class="category-btn" data-category="student" onclick="filterFAQs('student')">🎓 Students</button>
            <button class="category-btn" data-category="guide" onclick="filterFAQs('guide')">🗺️ Guides</button>
            <button class="category-btn" data-category="booking" onclick="filterFAQs('booking')">📅 Bookings</button>
            <button class="category-btn" data-category="payment" onclick="filterFAQs('payment')">💰 Payments</button>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <div class="section-header">
                <h2>❓ Frequently Asked Questions</h2>
                <p>Find quick answers to common questions about SmartTour services</p>
            </div>

            <div class="faq-grid" id="faqGrid">
                <!-- For Tourists -->
                <div class="faq-item" data-category="tourist">
                    <div class="faq-question">
                        How do I book a hotel or tour? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Simply search for your desired destination, select the hotel or tour that fits your needs, choose your dates, and click "Book Now". You'll receive an instant confirmation email!
                    </div>
                </div>

                <div class="faq-item" data-category="tourist">
                    <div class="faq-question">
                        Can I cancel or modify my booking? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, you can cancel or modify bookings up to 48 hours before check-in for a full refund. Some special offers may have different policies. Check your booking details for specific terms.
                    </div>
                </div>

                <div class="faq-item" data-category="tourist">
                    <div class="faq-question">
                        Is it safe to pay on SmartTour? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Absolutely! We use bank-level encryption and secure payment gateways. Your payment information is always protected and never stored on our servers.
                    </div>
                </div>

                <!-- For Students -->
                <div class="faq-item" data-category="student">
                    <div class="faq-question">
                        How do I apply for internships? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Browse the Job Opportunities section, find an internship that interests you, and click "Easy Apply". Upload your CV and our system will submit your application directly to employers.
                    </div>
                </div>

                <div class="faq-item" data-category="student">
                    <div class="faq-question">
                        Are there any free learning resources? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! We offer free tutorials, e-books, and webinar recordings. Check out our Learning Academy for free and premium content to boost your tourism career.
                    </div>
                </div>

                <div class="faq-item" data-category="student">
                    <div class="faq-question">
                        How can I get a certificate after completing a course? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Once you complete a course with 80% or higher, a certificate will be automatically generated. You can download it from your dashboard under "My Certificates".
                    </div>
                </div>

                <!-- For Guides -->
                <div class="faq-item" data-category="guide">
                    <div class="faq-question">
                        How do I become a verified tour guide? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Sign up as a guide, complete your profile with your credentials and experience, and submit verification documents. Our team will review and approve within 3-5 business days.
                    </div>
                </div>

                <div class="faq-item" data-category="guide">
                    <div class="faq-question">
                        How much can I earn as a tour guide? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Earnings vary based on tours, location, and experience. On average, guides earn LKR 25,000-60,000 per month. Top-rated guides earn significantly more!
                    </div>
                </div>

                <div class="faq-item" data-category="guide">
                    <div class="faq-question">
                        How do I manage my bookings? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Log in to your Guide Dashboard, where you can view all upcoming bookings, manage availability, update tour schedules, and communicate with travelers.
                    </div>
                </div>

                <!-- Bookings -->
                <div class="faq-item" data-category="booking">
                    <div class="faq-question">
                        How do I view my booking history? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Go to "My Bookings" in your dashboard. You'll see all your past and upcoming bookings including hotels, tours, and restaurant reservations.
                    </div>
                </div>

                <div class="faq-item" data-category="booking">
                    <div class="faq-question">
                        Will I receive a confirmation email? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, all successful bookings trigger an automatic confirmation email with all your booking details, including cancellation policies and contact information.
                    </div>
                </div>

                <!-- Payments -->
                <div class="faq-item" data-category="payment">
                    <div class="faq-question">
                        What payment methods do you accept? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        We accept all major credit cards (Visa, MasterCard, Amex), PayPal, and local bank transfers. Payments are processed securely through encrypted gateways.
                    </div>
                </div>

                <div class="faq-item" data-category="payment">
                    <div class="faq-question">
                        How do I get a refund? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Request a cancellation from your booking page. If eligible, refunds are processed within 5-7 business days to your original payment method.
                    </div>
                </div>

                <!-- Technical -->
                <div class="faq-item" data-category="technical">
                    <div class="faq-question">
                        I forgot my password. How do I reset it? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Click "Forgot Password" on the login page. Enter your email, and we'll send you a password reset link. Follow the instructions to create a new password.
                    </div>
                </div>

                <div class="faq-item" data-category="technical">
                    <div class="faq-question">
                        How do I update my profile information? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Log in and go to "My Profile" from your dashboard. You can edit personal information, change password, update preferences, and manage notifications.
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support Section -->
        <div class="contact-support">
            <h3>📞 Still Need Help?</h3>
            <p>Our support team is available 24/7 to assist you with any questions or issues.</p>
            
            <div class="contact-methods">
                <a href="#" class="contact-card" onclick="openChat()">
                    <i class="fas fa-comments"></i>
                    <div class="info">
                        <h4>Live Chat</h4>
                        <p>Chat with our team</p>
                    </div>
                </a>
                <a href="mailto:support@smarttour.com" class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <div class="info">
                        <h4>Email Support</h4>
                        <p>support@smarttour.com</p>
                    </div>
                </a>
                <a href="tel:+94771234567" class="contact-card">
                    <i class="fas fa-phone-alt"></i>
                    <div class="info">
                        <h4>24/7 Hotline</h4>
                        <p>+94 77 123 4567</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Submit a Ticket -->
        <div class="ticket-section">
            <h3>🎫 Submit a Support Ticket</h3>
            <p>Can't find what you're looking for? Submit a ticket and our team will get back to you within 24 hours.</p>
            
            <form class="ticket-form" id="ticketForm" onsubmit="submitTicket(event)">
                <div class="form-group">
                    <input type="text" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <select required>
                        <option value="">Select Issue Type</option>
                        <option value="booking">Booking Issue</option>
                        <option value="payment">Payment Problem</option>
                        <option value="account">Account Issue</option>
                        <option value="technical">Technical Problem</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea placeholder="Describe your issue in detail..." required></textarea>
                </div>
                <button type="submit" class="submit-ticket">Submit Ticket →</button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3>Ticket Submitted! 🎉</h3>
            <p>Your support ticket has been submitted successfully. Our team will respond within 24 hours.</p>
            <button class="modal-btn" onclick="closeSuccessModal()">Got it, thanks!</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© 2024 SmartTour | 24/7 Support for Tourists, Students & Guides</p>
            <p style="margin-top: 8px; font-size: 0.8rem;">✨ We're here to help make your journey amazing!</p>
        </div>
    </footer>

    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                faqItem.classList.toggle('active');
            });
        });

        // Filter FAQs by Category
        function filterFAQs(category) {
            const faqItems = document.querySelectorAll('.faq-item');
            const buttons = document.querySelectorAll('.category-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            faqItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Search FAQ
        function searchFAQ() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Submit Ticket
        function submitTicket(event) {
            event.preventDefault();
            document.getElementById('successModal').style.display = 'flex';
            document.getElementById('ticketForm').reset();
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        function openChat() {
            alert('💬 Live Chat\n\nOur support team is online!\nPlease describe your issue and we\'ll connect you with an agent.');
        }

       
        function filterByCategory(category) {
            filterFAQs(category);
            // Update active tab
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-category') === category) {
                    btn.classList.add('active');
                }
            });
        }

        window.onclick = function(event) {
            const modal = document.getElementById('successModal');
            if (event.target == modal) {
                closeSuccessModal();
            }
        }

        // Enter key search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFAQ();
            }
        });
    </script>
</body>
</html>