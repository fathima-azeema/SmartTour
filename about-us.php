<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/about-us.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>

<!-- Header -->
<header id="header" class="header">
    <div class="container">
        <div class="header-content">
            <a href="index.php" class="logo">
                <i class="fas fa-map-marked-alt logo-icon"></i>
                <span class="logo-text">SmartTour</span>
            </a>

            <nav class="nav-desktop">
                <a href="index.php" class="nav-link">Home</a>
                <a href="about-us.php" class="nav-link active">About</a>
                <a href="search-hotels.php" class="nav-link">Hotels</a>
                <a href="search-restaurants.php" class="nav-link">Restaurants</a>
                <a href="search-tours.php" class="nav-link">Tours</a>
                <a href="contact.php" class="nav-link">Contact</a>
            </nav>

            <div class="header-actions">
                <button class="btn btn-secondary" onclick="window.location.href='login.php'">Login</button>
                <button class="btn btn-primary" onclick="window.location.href='signup.php'">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="hero-about">
    <div class="container">
        <div class="hero-content">
            <h1>Discover the Story Behind SmartTour</h1>
            <p>Your trusted partner in creating unforgettable Sri Lankan adventures</p>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">50,000+</span>
                    <span class="stat-label">Happy Travelers</span>
                </div>
                <div class="stat">
                    <span class="stat-number">200+</span>
                    <span class="stat-label">Destinations</span>
                </div>
                <div class="stat">
                    <span class="stat-number">4.8</span>
                    <span class="stat-label">Rating</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-text" data-aos="fade-right">
                <h2>Welcome to SmartTour</h2>
                <p>At <strong>SmartTour</strong>, we believe that travel should be effortless, immersive, and extraordinary. Founded in 2018, we've been dedicated to showcasing the breathtaking beauty and rich cultural heritage of Sri Lanka to travelers from around the world.</p>
                <p>Our platform combines cutting-edge technology with local expertise to deliver personalized travel experiences that go beyond the ordinary. From pristine beaches to ancient temples, from misty mountains to vibrant cities - we help you discover the soul of Sri Lanka.</p>
                <p>We're not just a booking platform; we're your travel companion, ensuring every moment of your journey is memorable, safe, and truly authentic.</p>
            </div>
            <div class="about-image" data-aos="fade-left">
                <img src="https://www.tourcompass.co.uk/wp-content/uploads/cache/82/43/74/6df7145c-d749-5808-a2f2-dffe8c244ee5.webp" alt="Sri Lanka Landscape">
                <div class="experience-badge">
                    <span class="number">7+</span>
                    <span>Years of Excellence</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="mission-vision">
    <div class="container">
        <div class="mv-grid">
            <div class="mv-card" data-aos="fade-up" data-aos-delay="100">
                <div class="mv-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Our Mission</h3>
                <p>To transform travel in Sri Lanka by providing seamless access to authentic experiences, curated accommodations, and expert local guidance - all through an intuitive digital platform that puts travelers' needs first.</p>
            </div>
            <div class="mv-card" data-aos="fade-up" data-aos-delay="200">
                <div class="mv-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Our Vision</h3>
                <p>To become the most trusted travel companion in South Asia, known for innovation, sustainability, and creating meaningful connections between travelers and local communities.</p>
            </div>
            <div class="mv-card" data-aos="fade-up" data-aos-delay="300">
                <div class="mv-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Our Values</h3>
                <p>We operate with integrity, passion for travel, commitment to sustainability, and dedication to creating exceptional experiences that respect both our travelers and the destinations we showcase.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>What We Offer</h2>
            <p>Comprehensive travel solutions tailored to your preferences</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                <div class="service-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <h3>Luxury Stays</h3>
                <p>Discover handpicked accommodations from boutique hotels to luxury resorts, all verified for quality and service excellence.</p>
                <a href="search-hotels.php" class="service-link">Explore Hotels <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                <div class="service-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Culinary Experiences</h3>
                <p>From street food tours to fine dining, experience Sri Lanka's diverse cuisine with our curated restaurant recommendations.</p>
                <a href="search-restaurants.php" class="service-link">Find Restaurants <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                <div class="service-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Curated Tours</h3>
                <p>Explore cultural landmarks, adventure spots, and hidden gems with our expert local guides and carefully designed itineraries.</p>
                <a href="search-tours.php" class="service-link">Browse Tours <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                <div class="service-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Travel Safety</h3>
                <p>24/7 emergency support, real-time alerts, and comprehensive travel insurance options for complete peace of mind.</p>
                <a href="#" class="service-link">Safety Info <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>Why Travelers Choose SmartTour</h2>
            <p>Experience the difference with our platform</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-globe-asia"></i>
                </div>
                <h3>Local Expertise</h3>
                <p>Sri Lankan travel experts with deep knowledge of hidden gems</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="150">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Seamless Technology</h3>
                <p>Real-time booking, instant confirmations, mobile app access</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h3>Personalized Service</h3>
                <p>Tailored recommendations based on your preferences</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="250">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Round-the-clock customer service in multiple languages</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Sustainable Travel</h3>
                <p>Eco-friendly practices supporting local communities</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="350">
                <div class="feature-icon">
                    <i class="fas fa-award"></i>
                </div>
                <h3>Quality Assured</h3>
                <p>All partners carefully vetted for service excellence</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>Meet Our Leadership Team</h2>
            <p>Passionate professionals dedicated to transforming your travel experience</p>
        </div>
        
        <div class="team-grid">
            <div class="team-member" data-aos="fade-up" data-aos-delay="100">
                <div class="member-image">
                    <img src="images/azee.jpg" alt="John Doe">
                    <div class="member-social">
                        <a href="https://lk.linkedin.com/in/fathima-azeema"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://x.com/FathimaA72805"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <h3>Azee_ma</h3>
                <p class="member-role">CEO & Founder</p>
                <p class="member-bio">With over 5 years in the travel industry, Azee founded SmartTour to bridge the gap between technology and authentic travel experiences.</p>
            </div>
            
            <div class="team-member" data-aos="fade-up" data-aos-delay="200">
                <div class="member-image">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Jane Smith">
                    <div class="member-social">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <h3>Jane Smith</h3>
                <p class="member-role">Tour Experience Director</p>
                <p class="member-bio">Jane curates our unique tour experiences, combining her background in cultural anthropology with a passion for sustainable tourism.</p>
            </div>
            
            <div class="team-member" data-aos="fade-up" data-aos-delay="300">
                <div class="member-image">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="David Lee">
                    <div class="member-social">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <h3>David Lee</h3>
                <p class="member-role">Technology Director</p>
                <p class="member-bio">David leads our tech team in creating innovative solutions that make travel planning seamless and enjoyable for our customers.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2>Ready to Explore Sri Lanka with Us?</h2>
            <p>Join thousands of satisfied travelers who have discovered the magic of Sri Lanka through SmartTour</p>
            <div class="cta-buttons">
                <a href="search-tours.php" class="btn-white">Browse Tours</a>
                <a href="contact.php" class="btn-outline-white">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="js/about-us.js"></script>
</body>
</html>