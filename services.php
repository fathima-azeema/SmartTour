<?php
// services.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - SmartTour | Your Complete Travel Companion</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/services.css">
</head>
<body>
    <!-- Animated Travel Background Elements -->
    <div class="travel-bg">
        <div class="airplane"><i class="fas fa-plane"></i></div>
        <div class="airplane"><i class="fas fa-plane-departure"></i></div>
        <div class="airplane"><i class="fas fa-fan"></i></div>
        <div class="balloon"><i class="fas fa-hot-air-balloon"></i></div>
        <div class="balloon"><i class="fas fa-cloud"></i></div>
        <div class="balloon"><i class="fas fa-cloud-moon"></i></div>
        <div class="globe"><i class="fas fa-globe-asia"></i></div>
        <div class="compass"><i class="fas fa-compass"></i></div>
    </div>
    <div class="location-dot"></div>
    <div class="location-dot"></div>
    <div class="location-dot"></div>
    <div class="location-dot"></div>

    <!-- Header Section -->
    <header id="header" class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo Section -->
                <a href="index.php" class="logo">
                    <i class="fas fa-map-marked-alt logo-icon"></i>
                    <span class="logo-text">SmartTour</span>
                </a>

                <!-- Desktop Navigation -->
                <nav class="nav-desktop">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="about-us.php" class="nav-link">About</a>
                    <a href="services.php" class="nav-link active">Services</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- User Actions -->
                <div class="header-actions">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-user"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary" onclick="window.location.href='login.php'">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='signup.php'">
                            <i class="fas fa-user-plus"></i> Sign Up
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-header">
                <a href="index.php" class="logo">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="logo-text">SmartTour</span>
                </a>
                <button class="mobile-menu-close" id="mobileMenuClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="mobile-nav-links">
                <a href="index.php" class="mobile-nav-link">Home</a>
                <a href="about-us.php" class="mobile-nav-link">About</a>
                <a href="services.php" class="mobile-nav-link active">Services</a>
                <a href="contact.php" class="mobile-nav-link">Contact</a>
                <a href="dashboard.php" class="mobile-nav-link">Dashboard</a>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="mobile-nav-link">Login</a>
                    <a href="signup.php" class="mobile-nav-link">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <span class="hero-badge" data-aos="fade-up">
                        <i class="fas fa-crown"></i> Premium Services
                    </span>
                    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">
                        Your Complete Travel 
                        <span class="hero-title-gradient">Companion</span>
                        <span class="emoji">🌍</span>
                    </h1>
                    <p class="hero-description" data-aos="fade-up" data-aos-delay="200">
                        From booking your next adventure to building your career in tourism, 
                        we provide everything you need for an unforgettable experience.
                    </p>
                    
                    <div class="hero-stats" data-aos="fade-up" data-aos-delay="300">
                        <div class="hero-stat">
                            <span class="hero-stat-number">10K+</span>
                            <span class="hero-stat-label">Happy Travelers</span>
                        </div>
                        <div class="hero-stat">
                            <span class="hero-stat-number">500+</span>
                            <span class="hero-stat-label">Tour Packages</span>
                        </div>
                        <div class="hero-stat">
                            <span class="hero-stat-number">100+</span>
                            <span class="hero-stat-label">Expert Guides</span>
                        </div>
                    </div>
                    
                    <div class="hero-actions" data-aos="fade-up" data-aos-delay="400">
                        <a href="#services" class="hero-btn primary">
                            <i class="fas fa-rocket"></i> Explore Services
                        </a>
                        <a href="contact.php" class="hero-btn secondary">
                            <i class="fas fa-headset"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="services">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">
                        <i class="fas fa-star"></i> What We Offer
                    </span>
                    <h2 class="section-title">
                        Our <span>Premium</span> Services
                    </h2>
                    <p class="section-description">
                        Discover a world of possibilities with our comprehensive travel and tourism services
                    </p>
                </div>

                <div class="services-grid">
                    <!-- Hotel Booking -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h3 class="service-title">Hotel Booking</h3>
                        <p class="service-description">
                            Find and book the perfect accommodation with exclusive deals and instant confirmation.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i> 5000+ Hotels
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Best Price Guarantee
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Free Cancellation
                            </div>
                        </div>

                    </div>

                    <!-- Restaurant Reservations -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3 class="service-title">Dining Experiences</h3>
                        <p class="service-description">
                            Reserve tables at the finest restaurants and discover local culinary delights.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i> 1000+ Restaurants
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Local Cuisine
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Special Deals
                            </div>
                        </div>

                    </div>

                    <!-- Tour Packages -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3 class="service-title">Tour Packages</h3>
                        <p class="service-description">
                            Choose from a variety of guided tours to explore destinations with expert guides.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i> 500+ Tour Packages
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Expert Guides
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Customizable
                            </div>
                        </div>

                    </div>

                    <!-- Job Opportunities -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="service-title">Career Hub</h3>
                        <p class="service-description">
                            Find and apply for tourism industry jobs and internships worldwide.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i> 200+ Job Listings
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Career Resources
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Networking
                            </div>
                        </div>

                    </div>

                    <!-- Learning Resources -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="service-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="service-title">Learning Academy</h3>
                        <p class="service-description">
                            Access tutorials, e-books, and courses to boost your tourism career.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i> 100+ Courses
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Expert Instructors
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Certificates
                            </div>
                        </div>

                    </div>

                    <!-- Guide Services -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-icon">
                            <i class="fas fa-map-signs"></i>
                        </div>
                        <h3 class="service-title">Guide Network</h3>
                        <p class="service-description">
                            Connect with experienced guides or showcase your expertise as a guide.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Verified Guides
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Direct Booking
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i> Reviews & Ratings
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- For Tourists Section -->
        <section class="tourists-section">
            <div class="container">
                <div class="tourists-content">
                    <div class="tourists-text" data-aos="fade-right">
                        <span class="section-subtitle">
                            <i class="fas fa-umbrella-beach"></i> For Travelers
                        </span>
                        <h2>
                            Your <span>Ultimate Travel</span> Companion
                        </h2>
                        <p class="hero-description">
                            Get personalized recommendations, book hotels, restaurants, and tours with just a few clicks. 
                            Enjoy a hassle-free travel experience with our all-in-one platform.
                        </p>
                        
                        <div class="tourists-list">
                            <div class="tourists-item" data-aos="fade-up" data-aos-delay="100">
                                <i class="fas fa-hotel"></i>
                                <span>Hotels & Resorts</span>
                            </div>
                            <div class="tourists-item" data-aos="fade-up" data-aos-delay="150">
                                <i class="fas fa-utensils"></i>
                                <span>Restaurants</span>
                            </div>
                            <div class="tourists-item" data-aos="fade-up" data-aos-delay="200">
                                <i class="fas fa-map-marked-alt"></i>
                                <span>Guided Tours</span>
                            </div>
                            <div class="tourists-item" data-aos="fade-up" data-aos-delay="250">
                                <i class="fas fa-car"></i>
                                <span>Transportation</span>
                            </div>
                            <div class="tourists-item" data-aos="fade-up" data-aos-delay="300">
                                <i class="fas fa-ticket-alt"></i>
                                <span>Attractions</span>
                            </div>
                            <div class="tourists-item" data-aos="fade-up" data-aos-delay="350">
                                <i class="fas fa-shield-alt"></i>
                                <span>Travel Insurance</span>
                            </div>
                        </div>
                        
                        <a href="search-hotels.php" class="hero-btn primary" style="margin-top: var(--space-lg);">
                            <i class="fas fa-rocket"></i> Start Your Journey
                        </a>
                    </div>
                    
                    <div class="tourists-image" data-aos="fade-left">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800" 
                             alt="Luxury Hotel Pool">
                        <div class="tourists-badge">
                            <i class="fas fa-star"></i> 10K+ Happy Travelers
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Students Section -->
        <section class="students-section">
            <div class="container">
                <div class="students-content">
                    <div class="tourists-image" data-aos="fade-right">
                        <img src="https://media.youthincmag.com/images/30b8905d-d8d2-4dd8-aeed-b0f867173fcc/original.jpg" 
                             alt="Students Learning">
                        <div class="tourists-badge" style="background: var(--gradient-success);">
                            <i class="fas fa-graduation-cap"></i> 500+ Students Placed
                        </div>
                    </div>
                    
                    <div class="tourists-text" data-aos="fade-left">
                        <span class="section-subtitle">
                            <i class="fas fa-graduation-cap"></i> For Students
                        </span>
                        <h2>
                            Build Your <span>Tourism Career</span>
                        </h2>
                        <p class="hero-description">
                            Explore internships, connect with industry professionals, and access learning resources 
                            in the tourism industry. Start building your career today!
                        </p>
                        
                        <div class="students-stats">
                            <div class="stat-box" data-aos="zoom-in" data-aos-delay="100">
                                <span class="number">200+</span>
                                <span class="label">Internships</span>
                            </div>
                            <div class="stat-box" data-aos="zoom-in" data-aos-delay="200">
                                <span class="number">100+</span>
                                <span class="label">Courses</span>
                            </div>
                            <div class="stat-box" data-aos="zoom-in" data-aos-delay="300">
                                <span class="number">50+</span>
                                <span class="label">Partners</span>
                            </div>
                        </div>
                        
                        <a href="jobs.php" class="hero-btn primary" style="margin-top: var(--space-lg);">
                            <i class="fas fa-search"></i> Explore Opportunities
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Tour Guides Section -->
        <section class="guides-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">
                        <i class="fas fa-map-signs"></i> For Tour Guides
                    </span>
                    <h2 class="section-title">
                        Join Our <span>Guide Network</span>
                    </h2>
                    <p class="section-description">
                        Showcase your expertise, connect with travelers, and grow your guiding business
                    </p>
                </div>

                <div class="guides-grid">
                    <div class="guide-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="guide-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="guide-name">Create Profile</h3>
                        <p class="guide-specialty">Showcase your expertise</p>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p style="color: var(--gray-600); margin-bottom: var(--space-md);">
                            Build a professional profile highlighting your experience and specialties
                        </p>
                        <a href="signup.php" class="service-link">
                            Get Started <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="guide-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="guide-avatar">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="guide-name">Manage Bookings</h3>
                        <p class="guide-specialty">Track your schedule</p>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p style="color: var(--gray-600); margin-bottom: var(--space-md);">
                            Easily manage your availability and accept bookings from travelers
                        </p>
                        <a href="dashboard.php" class="service-link">
                            View Dashboard <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="guide-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="guide-avatar">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="guide-name">Grow Business</h3>
                        <p class="guide-specialty">Increase earnings</p>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p style="color: var(--gray-600); margin-bottom: var(--space-md);">
                            Get featured, receive reviews, and grow your guiding business
                        </p>
                        <a href="dashboard.php" class="service-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>

                    </div>
                </div>
                
                <div style="text-align: center; margin-top: var(--space-xl);" data-aos="fade-up">
                    <a href="signup.php" class="hero-btn primary">
                        <i class="fas fa-user-plus"></i> Create Your Profile
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content" data-aos="fade-up">
                    <h2 class="cta-title">Ready to Start Your Journey?</h2>
                    <p class="cta-description">
                        Join thousands of happy travelers, students, and guides who trust SmartTour for their travel needs.
                    </p>
                    <div class="cta-buttons">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="dashboard.php" class="cta-btn">
                                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                            </a>
                        <?php else: ?>
                            <a href="signup.php" class="cta-btn">
                                <i class="fas fa-user-plus"></i> Sign Up Now
                            </a>
                            <a href="login.php" class="cta-btn outline">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>SmartTour</span>
                    </div>
                    <p class="footer-description">
                        Your trusted partner for unforgettable Sri Lankan adventures. Experience the beauty, culture, and hospitality of Sri Lanka.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">Quick Links</h3>
                    <div class="footer-links">
                        <a href="index.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Home
                        </a>
                        <a href="about-us.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> About Us
                        </a>
                        <a href="services.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Services
                        </a>
                        <a href="contact.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Contact
                        </a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">Services</h3>
                    <div class="footer-links">
                        <a href="search-hotels.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Hotel Booking
                        </a>
                        <a href="search-tours.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Tour Packages
                        </a>
                        <a href="job-opportunities.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Job Opportunities
                        </a>
                        <a href="learning.php" class="footer-link">
                            <i class="fas fa-chevron-right"></i> Learning Resources
                        </a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">Contact Info</h3>
                    <div class="footer-links">
                        <a href="#" class="footer-link">
                            <i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka
                        </a>
                        <a href="tel:+94771234567" class="footer-link">
                            <i class="fas fa-phone"></i> +94 77 123 4567
                        </a>
                        <a href="mailto:info@smarttour.com" class="footer-link">
                            <i class="fas fa-envelope"></i> info@smarttour.com
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2023 SmartTour. All rights reserved. | Crafted with ❤️ for Sri Lankan Tourism</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuClose = document.getElementById('mobileMenuClose');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.add('active');
            });
        }

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
            });
        }

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                mobileMenu.classList.remove('active');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Counter animation for stats
        function animateCounter(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate stats when they come into view
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.hero-stat-number');
                    statNumbers.forEach(stat => {
                        const value = stat.textContent.replace(/[^0-9]/g, '');
                        if (value) {
                            animateCounter(stat, 0, parseInt(value), 2000);
                        }
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            observer.observe(heroSection);
        }

        // Add hover effect to service cards
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Dynamic year in footer
        document.querySelector('.footer-bottom p').innerHTML = 
            `&copy; ${new Date().getFullYear()} SmartTour. All rights reserved. | Crafted with ❤️ for Sri Lankan Tourism`;
    </script>
</body>
</html>