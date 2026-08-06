<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/contact.css">

</head>
<body>

<!-- Header Section -->
<header id="header" class="header sticky">
    <div class="container">
        <div class="header-content">
            <!-- Logo Section -->
            <a href="index.html" class="logo">
                <i class="fas fa-map-marked-alt logo-icon"></i>
                <span class="logo-text">SmartTour</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="nav-desktop">
                <a href="index.php" class="nav-link">Home</a>
                <a href="about-us.php" class="nav-link">About</a>
                <a href="services.php" class="nav-link">Services</a>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="contact.php" class="nav-link active">Contact</a>
            </nav>

            <!-- User Actions (Login and Sign Up) -->
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="window.location.href='login.php'">Login</button>
                <button class="btn btn-primary" onclick="window.location.href='signup.php'">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="hero-content">
            <h1>Get In Touch With Us</h1>
            <p>We're here to help you plan your perfect Sri Lankan adventure. Reach out with any questions or inquiries.</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <h2 class="section-title">Contact SmartTour</h2>
        <p class="section-subtitle">Have questions about our tours or services? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        
        <div class="contact-container">
            <!-- Contact Information -->
            <div class="contact-info">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Our Location</h3>
                        <p>123 Galle Road, Colombo 03,<br>Sri Lanka</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Phone Number</h3>
                        <p>+94 11 234 5678<br>+94 77 123 4567 (24/7 Hotline)</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email Address</h3>
                        <p>info@smarttour.lk<br>support@smarttour.lk</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Working Hours</h3>
                        <p>Monday - Friday: 8:00 AM - 8:00 PM<br>Weekends: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form">
                <form id="contactForm">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" placeholder="Enter your email address" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" id="phone" class="form-control" placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <input type="text" id="subject" class="form-control" placeholder="What is this regarding?" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Your Message</label>
                        <textarea id="message" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <h2 class="section-title">Find Us</h2>
        <p class="section-subtitle">Visit our office in Colombo or get directions to our location</p>
        
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.798511757687!2d79.85297541532638!3d6.914657295003692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2596b2c5a01c5%3A0x69efbc0e85e03f06!2sGalle%20Rd%2C%20Colombo!5e0!3m2!1sen!2slk!4v1647266782842!5m2!1sen!2slk" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <p class="section-subtitle">Find quick answers to common questions about our services</p>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    How far in advance should I book my tour?
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>We recommend booking at least 2-3 weeks in advance for domestic tours and 4-6 weeks for international tours to ensure availability and the best prices. For peak season (December to April), we suggest booking even earlier.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    What is your cancellation policy?
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>Cancellations made 30 days or more before the tour date receive a full refund. Between 15-29 days, we offer a 50% refund. Cancellations within 14 days of the tour are non-refundable, though we try to be flexible in case of emergencies.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    Do you offer customized tour packages?
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>Yes! We specialize in creating personalized tour experiences. Contact us with your preferences, budget, and travel dates, and our travel experts will design a custom itinerary just for you.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    What payment methods do you accept?
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>We accept all major credit cards (Visa, MasterCard, American Express), bank transfers, and popular payment platforms like PayPal. For local bookings, we also accept cash payments at our office.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    Do you provide airport pickup services?
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>Yes, we offer airport pickup and drop-off services from Bandaranaike International Airport (CMB) for all our tour packages. This can be added to any booking for an additional fee.</p>
                </div>
            </div>
        </div>
    </div>
</section>

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
    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // FAQ Toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                // Close all other FAQ items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.faq-answer').classList.remove('active');
                    }
                });
                
                // Toggle current item
                item.classList.toggle('active');
                const answer = item.querySelector('.faq-answer');
                answer.classList.toggle('active');
            });
        });
        
        // Form submission
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const subject = document.getElementById('subject').value;
                const message = document.getElementById('message').value;
                
                if (!name || !email || !subject || !message) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // In a real application, you would submit the form to a server
                alert('Thank you for your message! We will get back to you soon.');
                contactForm.reset();
            });
        }
    });
</script>

</body>
</html>