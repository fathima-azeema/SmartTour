<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartTour - Discover Your Next Adventure</title>
    <meta name="description" content="Your ultimate platform for smart tourism experiences, connecting tourists, students, and professionals worldwide.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/index.css">
<style></style>
</head>
<body>
    <header id="header" class="header sticky">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-map-marked-alt logo-icon"></i>
                    <span class="logo-text">SmartTour</span>
                </a>

                <nav class="nav-desktop">
                    <a href="index.php" class="nav-link active">Home</a>
                    <a href="about-us.php" class="nav-link">About</a>
                    <a href="services.php" class="nav-link">Services</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                </nav>

                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='login.php'">Login</button>
                    <button class="btn btn-primary" onclick="window.location.href='signup.php'">Sign Up</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section" id="heroSection">
        <div class="hero-carousel">
            <div class="hero-slide active">
                <img src="https://deluxholidays.com/wp-content/uploads/2023/09/Wasgamuwa-National-Park-2.jpg" alt="Tropical Paradise" class="hero-image">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">Discover Your Next Adventure</h1>
                    <p class="hero-subtitle">Explore pristine beaches and luxury resorts worldwide</p>
                    <button class="btn btn-primary btn-lg" onclick="window.location.href='about-us.php'">
                        <i class="fas fa-sparkles"></i> Start Your Journey
                    </button>
                </div>
            </div>

            <div class="hero-slide">
                <img src="https://images.unsplash.com/photo-1609861517208-e5b7b4cd4b87?w=1920&q=80" alt="Mountain Adventures" class="hero-image">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">Explore the World with Ease</h1>
                    <p class="hero-subtitle">Experience thrilling mountain expeditions and adventures</p>
                    <button class="btn btn-primary btn-lg" onclick="window.location.href='about-us.php'">
                        <i class="fas fa-sparkles"></i> Start Your Journey
                    </button>
                </div>
            </div>

            <div class="hero-slide">
                <img src="https://images.unsplash.com/photo-1517144447511-aebb25bbc5fa?w=1920&q=80" alt="Urban Exploration" class="hero-image">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <h1 class="hero-title">Start Your Journey Today</h1>
                    <p class="hero-subtitle">Discover vibrant cities and immerse in diverse cultures</p>
                    <button class="btn btn-primary btn-lg" onclick="window.location.href='about-us.php'">
                        <i class="fas fa-sparkles"></i> Start Your Journey
                    </button>
                </div>
            </div>
        </div>

        <button class="carousel-control prev" onclick="changeSlide(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="carousel-control next" onclick="changeSlide(1)">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div class="carousel-indicators">
            <button class="indicator active" onclick="goToSlide(0)"></button>
            <button class="indicator" onclick="goToSlide(1)"></button>
            <button class="indicator" onclick="goToSlide(2)"></button>
        </div>
    </section>


    <!-- Destinations Section -->
    <div class="destinations-modern">
        <div class="destinations-header">
            
            <h1 class="main-title">Popular Sri Lankan Destinations</h1>
            <p class="tagline">Discover the beauty and culture of Sri Lanka's most iconic locations</p>
        </div>
        
        <div class="destinations-grid">
            <!-- Sigiriya Card -->
            <div class="destination-card" onclick="openHotelBooking('Sigiriya Rock Fortress', 'sigiriya', 12500)">
                <div class="card-image-wrapper">
                    <img src="https://i.pinimg.com/736x/0e/ed/2a/0eed2a5464d29acbc937f032b3c4a2ee.jpg" 
                         alt="Sigiriya Rock Fortress" class="destination-img">
                    <div class="destination-badge">100+ Tours</div>
                    <div class="destination-overlay"></div>
                </div>
                <div class="card-content">
                    <div class="location-tag">Sigiriya</div>
                    <h3 class="destination-title">Sigiriya Rock Fortress</h3>
                    <p class="destination-desc">Ancient rock fortress with stunning views and rich history</p>
                    <button class="cta-button" onclick="event.stopPropagation(); openHotelBooking('Sigiriya Rock Fortress', 'sigiriya', 12500)">
                        <span>Explore Sigiriya</span>
                        <svg class="arrow-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Colombo Card -->
            <div class="destination-card" onclick="openHotelBooking('Colombo City Hotel', 'colombo', 18500)">
                <div class="card-image-wrapper">
                    <img src="https://islandwyde.com/wp-content/uploads/2025/08/a90aa71efd.jpg" 
                         alt="Colombo City" class="destination-img">
                    <div class="destination-badge">120+ Tours</div>
                    <div class="destination-overlay"></div>
                </div>
                <div class="card-content">
                    <div class="location-tag">Colombo</div>
                    <h3 class="destination-title">Colombo City</h3>
                    <p class="destination-desc">Vibrant capital city with colonial architecture and modern life</p>
                    <button class="cta-button" onclick="event.stopPropagation(); openHotelBooking('Colombo City Hotel', 'colombo', 18500)">
                        <span>Explore Colombo</span>
                        <svg class="arrow-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Kandy Card -->
            <div class="destination-card" onclick="openHotelBooking('Kandy Lake View Hotel', 'kandy', 14500)">
                <div class="card-image-wrapper">
                    <img src="https://media-cdn.tripadvisor.com/media/photo-s/19/0b/ff/73/picturesquely-located.jpg" 
                         alt="Sacred City of Kandy" class="destination-img">
                    <div class="destination-badge">80+ Tours</div>
                    <div class="destination-overlay"></div>
                </div>
                <div class="card-content">
                    <div class="location-tag">Kandy</div>
                    <h3 class="destination-title">Sacred City of Kandy</h3>
                    <p class="destination-desc">Spiritual heart of Sri Lanka with the Temple of the Sacred Tooth</p>
                    <button class="cta-button" onclick="event.stopPropagation(); openHotelBooking('Kandy Lake View Hotel', 'kandy', 14500)">
                        <span>Explore Kandy</span>
                        <svg class="arrow-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Deals Section -->
    <section class="special-deals">
        <div class="container">
            <h2>Special Deals</h2>
            <p>Book now and save on amazing Sri Lankan experiences</p>
            <p><h2></h2></p>
            <div class="deals-grid">
                <div class="deal-card" onclick="openHotelBooking('Cultural Triangle Tour Package', 'sigiriya', 42000)">
                    <div class="deal-image">
                        <img src="https://www.talallahouse.com/wp-content/uploads/2019/10/4P0A7031.jpg" alt="Cultural Triangle Tour">
                        <div class="deal-badge">30% OFF</div>
                    </div>
                    <div class="deal-content">
                        <h4>Cultural Triangle Tour</h4>
                        <p>5 Days / 4 Nights</p>
                        <p class="original-price">LKR 60,000 <span class="discount">30% OFF</span></p>
                        <p class="current-price">LKR 42,000</p>
                        <button class="btn btn-primary" style="width: 100%;" onclick="event.stopPropagation(); openHotelBooking('Cultural Triangle Tour Package', 'sigiriya', 42000)">Book Now</button>
                    </div>
                </div>
                
                <div class="deal-card" onclick="openHotelBooking('Beach Resort Package', 'mirissa', 33750)">
                    <div class="deal-image">
                        <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Beach Resort Package">
                        <div class="deal-badge">25% OFF</div>
                    </div>
                    <div class="deal-content">
                        <h4>Beach Resort Package</h4>
                        <p>3 Days / 2 Nights</p>
                        <p class="original-price">LKR 45,000 <span class="discount">25% OFF</span></p>
                        <p class="current-price">LKR 33,750</p>
                        <button class="btn btn-primary" style="width: 100%;" onclick="event.stopPropagation(); openHotelBooking('Beach Resort Package', 'mirissa', 33750)">Book Now</button>
                    </div>
                </div>
                
                <div class="deal-card" onclick="openHotelBooking('Hill Country Adventure', 'nuwaraeliya', 44000)">
                    <div class="deal-image">
                        <img src="https://i0.wp.com/thelandofwanderlust.com/wp-content/uploads/2025/07/514659794_832586715960069_6484689975201655604_n.jpg" alt="Hill Country Adventure">
                        <div class="deal-badge">20% OFF</div>
                    </div>
                    <div class="deal-content">
                        <h4>Hill Country Adventure</h4>
                        <p>4 Days / 3 Nights</p>
                        <p class="original-price">LKR 55,000 <span class="discount">20% OFF</span></p>
                        <p class="current-price">LKR 44,000</p>
                        <button class="btn btn-primary" style="width: 100%;" onclick="event.stopPropagation(); openHotelBooking('Hill Country Adventure', 'nuwaraeliya', 44000)">Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="container">
            <h2>Why Choose SmartTour?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4>Fast Booking</h4>
                    <p>Instant booking with real-time availability and confirmations</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h4>Personalized Experience</h4>
                    <p>Tailored recommendations based on your preferences and travel history</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Secure Payments</h4>
                    <p>Bank-level encryption and secure payment processing for peace of mind</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe-asia"></i>
                    </div>
                    <h4>Global Reach</h4>
                    <p>Access to thousands of destinations and experiences worldwide</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Platform Sections -->
    <section class="platform-sections">
        <div class="container">
            <div class="platform-grid">
                <div class="platform-card">
                    <div class="platform-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>For Tourists</h3>
                    <p>Book hotels, restaurants, and tours. Get personalized recommendations and emergency support for a worry-free travel experience.</p>
                </div>
                
                <div class="platform-card">
                    <div class="platform-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>For Students</h3>
                    <p>Explore internships, connect with industry professionals, and access learning resources to kickstart your tourism career.</p>
                </div>
                
                <div class="platform-card">
                    <div class="platform-icon">
                        <i class="fas fa-map-signs"></i>
                    </div>
                    <h3>For Tour Guides</h3>
                    <p>Create your professional profile, manage bookings, and promote your tours to travelers from around the world.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book Your Stay</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="hotelDetails" class="hotel-detail"></div>
                
                <form id="bookingForm" class="booking-form" onsubmit="processBooking(event)">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="guestName" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="guestEmail" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" id="guestPhone" placeholder="Enter your phone number" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Check-in Date</label>
                        <input type="date" id="checkinDate" required onchange="updateTotal()">
                    </div>
                    
                    <div class="form-group">
                        <label>Check-out Date</label>
                        <input type="date" id="checkoutDate" required onchange="updateTotal()">
                    </div>
                    
                    <div class="form-group">
                        <label>Number of Guests</label>
                        <select id="guests" onchange="updateTotal()">
                            <option value="1">1 Guest</option>
                            <option value="2" selected>2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5 Guests</option>
                            <option value="6">6+ Guests</option>
                        </select>
                    </div>
                    
                    <div class="price-summary" id="priceSummary"></div>
                    
                    <button type="submit" class="btn-book">
                        <i class="fas fa-check-circle"></i> Confirm Booking
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Search tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab');
    const form = document.getElementById('searchForm');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding content
            const tabId = this.dataset.tab;
            tabContents.forEach(content => content.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            // Update form action
            const action = this.dataset.action;
            form.action = action;
        });
    });
});
        // Carousel functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const indicators = document.querySelectorAll('.indicator');

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            currentSlide = (n + slides.length) % slides.length;
            
            slides[currentSlide].classList.add('active');
            indicators[currentSlide].classList.add('active');
        }

        function changeSlide(n) {
            showSlide(currentSlide + n);
        }

        function goToSlide(n) {
            showSlide(n);
        }

        // Auto slide every 5 seconds
        setInterval(() => {
            changeSlide(1);
        }, 5000);

        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.search-section .tab');
            const contents = document.querySelectorAll('.search-section .tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const id = tab.getAttribute('data-tab');

                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    tab.classList.add('active');
                    document.getElementById(id).classList.add('active');
                });
            });

            // Set min dates for check-in/out
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('checkinDate').min = today;
            document.getElementById('checkoutDate').min = today;
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Hotel data
        let currentHotel = {
            name: '',
            location: '',
            pricePerNight: 0,
            image: ''
        };

        // Hotel images by location
        const hotelImages = {
            sigiriya: 'https://i.pinimg.com/736x/0e/ed/2a/0eed2a5464d29acbc937f032b3c4a2ee.jpg',
            colombo: 'https://islandwyde.com/wp-content/uploads/2025/08/a90aa71efd.jpg',
            kandy: 'https://media-cdn.tripadvisor.com/media/photo-s/19/0b/ff/73/picturesquely-located.jpg',
            mirissa: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800',
            nuwaraeliya: 'https://i0.wp.com/thelandofwanderlust.com/wp-content/uploads/2025/07/514659794_832586715960069_6484689975201655604_n.jpg'
        };

        // Hotel descriptions
        const hotelDescriptions = {
            sigiriya: 'Experience the ancient rock fortress of Sigiriya. Our hotel offers stunning views, modern amenities, and easy access to this UNESCO World Heritage site. Perfect for history enthusiasts and nature lovers.',
            colombo: 'Stay in the heart of Sri Lanka\'s vibrant capital. Our hotel provides luxurious accommodations, fine dining, and easy access to shopping, dining, and cultural attractions.',
            kandy: 'Overlooking the beautiful Kandy Lake, our hotel offers serene accommodations with traditional Sri Lankan hospitality. Close to the Temple of the Sacred Tooth Relic.',
            mirissa: 'Beachfront paradise awaits! Enjoy stunning ocean views, fresh seafood, and easy access to whale watching tours and beautiful beaches.',
            nuwaraeliya: 'Experience the charm of "Little England" in our colonial-style hotel. Enjoy cool climate, tea plantations, and breathtaking mountain views.'
        };

        function openHotelBooking(hotelName, location, pricePerNight) {
            currentHotel = {
                name: hotelName,
                location: location,
                pricePerNight: pricePerNight,
                image: hotelImages[location] || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800'
            };
            
            // Set default dates
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            document.getElementById('checkinDate').value = today.toISOString().split('T')[0];
            document.getElementById('checkoutDate').value = tomorrow.toISOString().split('T')[0];
            
            // Update hotel details in modal
            const hotelDetailsHtml = `
                <img src="${currentHotel.image}" alt="${currentHotel.name}">
                <h3>${currentHotel.name}</h3>
                <div class="hotel-location">
                    <i class="fas fa-map-marker-alt"></i> ${currentHotel.location.charAt(0).toUpperCase() + currentHotel.location.slice(1)}, Sri Lanka
                </div>
                <div class="hotel-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span> (4.5/5 based on 128 reviews)</span>
                </div>
                <div class="hotel-description">
                    ${hotelDescriptions[location] || 'Experience comfort and luxury at our premier hotel. Enjoy world-class amenities, exceptional service, and unforgettable experiences.'}
                </div>
                <div class="hotel-price">
                    <strong>Price per night:</strong> LKR ${pricePerNight.toLocaleString()}
                </div>
            `;
            
            document.getElementById('hotelDetails').innerHTML = hotelDetailsHtml;
            
            // Show modal
            document.getElementById('bookingModal').classList.add('active');
            
            // Update total price
            updateTotal();
        }

        function updateTotal() {
            const checkin = new Date(document.getElementById('checkinDate').value);
            const checkout = new Date(document.getElementById('checkoutDate').value);
            const guests = parseInt(document.getElementById('guests').value);
            
            if (checkin && checkout && checkout > checkin) {
                const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
                const subtotal = currentHotel.pricePerNight * nights;
                const tax = subtotal * 0.12; // 12% tax
                const serviceCharge = subtotal * 0.05; // 5% service charge
                const total = subtotal + tax + serviceCharge;
                
                const priceSummaryHtml = `
                    <p><strong>Price Breakdown:</strong></p>
                    <p>LKR ${currentHotel.pricePerNight.toLocaleString()} × ${nights} night(s) = LKR ${subtotal.toLocaleString()}</p>
                    <p>Tax (12%): LKR ${tax.toLocaleString()}</p>
                    <p>Service Charge (5%): LKR ${serviceCharge.toLocaleString()}</p>
                    <p class="total-price"><strong>Total Amount:</strong> LKR ${total.toLocaleString()}</p>
                    <p><small>* Includes all taxes and charges</small></p>
                `;
                
                document.getElementById('priceSummary').innerHTML = priceSummaryHtml;
                return total;
            } else {
                document.getElementById('priceSummary').innerHTML = '<p>Please select valid dates</p>';
                return 0;
            }
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }

        function processBooking(event) {
            event.preventDefault();
            
            const name = document.getElementById('guestName').value;
            const email = document.getElementById('guestEmail').value;
            const phone = document.getElementById('guestPhone').value;
            const checkin = document.getElementById('checkinDate').value;
            const checkout = document.getElementById('checkoutDate').value;
            const guests = document.getElementById('guests').value;
            const total = updateTotal();
            
            // Validate inputs
            if (!name || !email || !phone || !checkin || !checkout) {
                showToast('Please fill in all fields', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showToast('Please enter a valid email address', 'error');
                return;
            }
            
            // Create booking object
            const booking = {
                hotel: currentHotel.name,
                location: currentHotel.location,
                guestName: name,
                guestEmail: email,
                guestPhone: phone,
                checkin: checkin,
                checkout: checkout,
                guests: guests,
                total: total,
                bookingDate: new Date().toISOString(),
                bookingId: 'BK' + Math.random().toString(36).substr(2, 8).toUpperCase()
            };
            
            // Save to localStorage (simulate database)
            let bookings = JSON.parse(localStorage.getItem('bookings') || '[]');
            bookings.push(booking);
            localStorage.setItem('bookings', JSON.stringify(bookings));
            
            // Show success message
            showToast(`Booking confirmed! Booking ID: ${booking.bookingId}`, 'success');
            
            // Close modal
            closeModal();
            
            // Reset form
            document.getElementById('bookingForm').reset();
            
            // Redirect to dashboard after 2 seconds (if user is logged in)
            setTimeout(() => {
                if (confirm('Booking confirmed! Would you like to view your bookings?')) {
                    window.location.href = 'view-bookings.php';
                }
            }, 1000);
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function subscribeNewsletter(event) {
            event.preventDefault();
            const email = event.target.querySelector('.newsletter-input').value;
            if (email && isValidEmail(email)) {
                showToast('Successfully subscribed to newsletter!', 'success');
                event.target.reset();
            } else {
                showToast('Please enter a valid email address', 'error');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
<!-- Footer -->
<?php include 'footer.php'; ?>
</body>
</html>
