<?php
// offers.php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['first_name'] ?? 'Student') : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offers - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/offer.css">
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
                        <span style="color: var(--gray-600);">Welcome, <?php echo htmlspecialchars($user_name); ?> 🎓</span>
                    <?php else: ?>
                        <a href="login.php" style="text-decoration: none; color: var(--primary);">Login for Exclusive Offers</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>🎁 Special Offers Just For You</h1>
            <p>Exclusive deals and discounts available for students. Save on tours, hotels, and amazing experiences!</p>
        </div>
    </section>

    <div class="container">
        <!-- Stats Banner -->
        <div class="stats-banner">
            <div class="stat-item">
                <div class="stat-number">Up to 50%</div>
                <div class="stat-label">Student Discount</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Student Support</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">Free</div>
                <div class="stat-label">Cancellation</div>
            </div>
        </div>

        <!-- Section Header -->
        <div class="section-header">
            <h2>🔥 Limited Time Offers</h2>
            <p>Grab these exclusive deals before they expire!</p>
        </div>

        <!-- Offers Grid -->
        <div class="offers-grid">
            <!-- Offer 1 - Hotel -->
            <div class="offer-card">
                <div class="offer-image" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=500&q=80');">
                    <div class="offer-badge hotel">🏨 Hotel</div>
                    <div class="discount-badge">🔥 30% OFF</div>
                </div>
                <div class="offer-content">
                    <h3 class="offer-title">Cinnamon Grand Colombo</h3>
                    <div class="offer-location">
                        <i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka
                        <span>⭐ 4.8</span>
                    </div>
                    <div class="offer-price">
                        <span class="original-price">LKR 25,000</span>
                        <div class="current-price">LKR 17,500 <span>/night</span></div>
                    </div>
                    <div class="offer-features">
                        <span><i class="fas fa-wifi"></i> Free WiFi</span>
                        <span><i class="fas fa-swimming-pool"></i> Pool</span>
                        <span><i class="fas fa-utensils"></i> Breakfast</span>
                    </div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number">23</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">45</span>
                            <span class="countdown-label">Mins</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">32</span>
                            <span class="countdown-label">Secs</span>
                        </div>
                    </div>
                    <button class="offer-btn" onclick="claimOffer('Cinnamon Grand Colombo', '30% OFF')">
                        <i class="fas fa-gift"></i> Claim Offer
                    </button>
                </div>
            </div>

            <!-- Offer 2 - Tour -->
            <div class="offer-card">
                <div class="offer-image" style="background-image: url('https://images.unsplash.com/photo-1598974357801-cbca100e65d3?auto=format&fit=crop&w=500&q=80');">
                    <div class="offer-badge tour">🗺️ Tour</div>
                    <div class="discount-badge">🔥 25% OFF</div>
                </div>
                <div class="offer-content">
                    <h3 class="offer-title">Cultural Triangle Tour</h3>
                    <div class="offer-location">
                        <i class="fas fa-map-marker-alt"></i> Sigiriya, Dambulla
                        <span>⭐ 4.9</span>
                    </div>
                    <div class="offer-price">
                        <span class="original-price">LKR 45,000</span>
                        <div class="current-price">LKR 33,750 <span>/person</span></div>
                    </div>
                    <div class="offer-features">
                        <span><i class="fas fa-clock"></i> 5 Days</span>
                        <span><i class="fas fa-user-friends"></i> Small Group</span>
                        <span><i class="fas fa-utensils"></i> Meals Included</span>
                    </div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number">47</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">12</span>
                            <span class="countdown-label">Mins</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">08</span>
                            <span class="countdown-label">Secs</span>
                        </div>
                    </div>
                    <button class="offer-btn" onclick="claimOffer('Cultural Triangle Tour', '25% OFF')">
                        <i class="fas fa-gift"></i> Claim Offer
                    </button>
                </div>
            </div>

            <!-- Offer 3 - Experience -->
            <div class="offer-card">
                <div class="offer-image" style="background-image: url('https://images.unsplash.com/photo-1559666647-215d6616eaa3?auto=format&fit=crop&w=500&q=80');">
                    <div class="offer-badge experience">🏖️ Experience</div>
                    <div class="discount-badge">🔥 40% OFF</div>
                </div>
                <div class="offer-content">
                    <h3 class="offer-title">Whale Watching Adventure</h3>
                    <div class="offer-location">
                        <i class="fas fa-map-marker-alt"></i> Mirissa, Sri Lanka
                        <span>⭐ 4.8</span>
                    </div>
                    <div class="offer-price">
                        <span class="original-price">LKR 15,000</span>
                        <div class="current-price">LKR 9,000 <span>/person</span></div>
                    </div>
                    <div class="offer-features">
                        <span><i class="fas fa-clock"></i> 4 Hours</span>
                        <span><i class="fas fa-ship"></i> Boat Tour</span>
                        <span><i class="fas fa-camera"></i> Photo Ops</span>
                    </div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number">12</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">30</span>
                            <span class="countdown-label">Mins</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">15</span>
                            <span class="countdown-label">Secs</span>
                        </div>
                    </div>
                    <button class="offer-btn" onclick="claimOffer('Whale Watching Adventure', '40% OFF')">
                        <i class="fas fa-gift"></i> Claim Offer
                    </button>
                </div>
            </div>
        </div>

        <!-- More Offers Row 2 -->
        <div class="offers-grid">
            <!-- Offer 4 - Weekend Getaway -->
            <div class="offer-card">
                <div class="offer-image" style="background-image: url('https://images.unsplash.com/photo-1580519542036-c47de6196ba5?auto=format&fit=crop&w=500&q=80');">
                    <div class="offer-badge hotel">🏨 Hotel</div>
                    <div class="discount-badge">🔥 35% OFF</div>
                </div>
                <div class="offer-content">
                    <h3 class="offer-title">Ella Nature Resort</h3>
                    <div class="offer-location">
                        <i class="fas fa-map-marker-alt"></i> Ella, Sri Lanka
                        <span>⭐ 4.7</span>
                    </div>
                    <div class="offer-price">
                        <span class="original-price">LKR 18,000</span>
                        <div class="current-price">LKR 11,700 <span>/night</span></div>
                    </div>
                    <div class="offer-features">
                        <span><i class="fas fa-mountain"></i> Mountain View</span>
                        <span><i class="fas fa-hot-tub"></i> Spa</span>
                        <span><i class="fas fa-utensils"></i> Dinner</span>
                    </div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number">18</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">22</span>
                            <span class="countdown-label">Mins</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">45</span>
                            <span class="countdown-label">Secs</span>
                        </div>
                    </div>
                    <button class="offer-btn" onclick="claimOffer('Ella Nature Resort', '35% OFF')">
                        <i class="fas fa-gift"></i> Claim Offer
                    </button>
                </div>
            </div>

            <!-- Offer 5 - Food Tour -->
            <div class="offer-card">
                <div class="offer-image" style="background-image: url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=500&q=80');">
                    <div class="offer-badge experience">🍜 Food Tour</div>
                    <div class="discount-badge">🔥 20% OFF</div>
                </div>
                <div class="offer-content">
                    <h3 class="offer-title">Street Food Experience</h3>
                    <div class="offer-location">
                        <i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka
                        <span>⭐ 4.9</span>
                    </div>
                    <div class="offer-price">
                        <span class="original-price">LKR 8,000</span>
                        <div class="current-price">LKR 6,400 <span>/person</span></div>
                    </div>
                    <div class="offer-features">
                        <span><i class="fas fa-clock"></i> 3 Hours</span>
                        <span><i class="fas fa-utensils"></i> 10+ Tastings</span>
                        <span><i class="fas fa-user-friends"></i> Local Guide</span>
                    </div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number">8</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">45</span>
                            <span class="countdown-label">Mins</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">30</span>
                            <span class="countdown-label">Secs</span>
                        </div>
                    </div>
                    <button class="offer-btn" onclick="claimOffer('Street Food Experience', '20% OFF')">
                        <i class="fas fa-gift"></i> Claim Offer
                    </button>
                </div>
            </div>

            <!-- Offer 6 - Safari -->
            <div class="offer-card">
                <div class="offer-image" style="background-image: url('https://images.unsplash.com/photo-1575363374538-03146d7a7322?auto=format&fit=crop&w=500&q=80');">
                    <div class="offer-badge tour">🐘 Safari</div>
                    <div class="discount-badge">🔥 25% OFF</div>
                </div>
                <div class="offer-content">
                    <h3 class="offer-title">Yala National Park Safari</h3>
                    <div class="offer-location">
                        <i class="fas fa-map-marker-alt"></i> Yala, Sri Lanka
                        <span>⭐ 4.8</span>
                    </div>
                    <div class="offer-price">
                        <span class="original-price">LKR 12,000</span>
                        <div class="current-price">LKR 9,000 <span>/person</span></div>
                    </div>
                    <div class="offer-features">
                        <span><i class="fas fa-clock"></i> Full Day</span>
                        <span><i class="fas fa-camera"></i> Photography</span>
                        <span><i class="fas fa-utensils"></i> Lunch Included</span>
                    </div>
                    <div class="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number">36</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">15</span>
                            <span class="countdown-label">Mins</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number">20</span>
                            <span class="countdown-label">Secs</span>
                        </div>
                    </div>
                    <button class="offer-btn" onclick="claimOffer('Yala National Park Safari', '25% OFF')">
                        <i class="fas fa-gift"></i> Claim Offer
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Exclusive Banner -->
        <div class="student-exclusive">
            <i class="fas fa-user-graduate"></i>
            <h3>🎓 Student Exclusive Deals</h3>
            <p>Verify your student email to unlock an additional 10% off on all offers! Plus free upgrade on select tours.</p>
            <button class="student-verify-btn" onclick="verifyStudent()">
                <i class="fas fa-envelope"></i> Verify Student Status
            </button>
        </div>

        <!-- Newsletter Section -->
        <div class="newsletter">
            <h3>📧 Never Miss a Deal</h3>
            <p>Subscribe to get exclusive student offers delivered straight to your inbox</p>
            <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                <input type="email" class="newsletter-input" placeholder="Enter your student email" required>
                <button type="submit" class="newsletter-btn">Subscribe →</button>
            </form>
        </div>
    </div>

    <!-- Claim Offer Modal -->
    <div id="claimModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3>Offer Claimed! 🎉</h3>
            <p id="claimMessage">You have successfully claimed the offer!</p>
            <p style="color: var(--gray-500); font-size: 0.9rem;">A confirmation email has been sent to your inbox with the promo code.</p>
            <button class="modal-btn" onclick="closeClaimModal()">Continue Exploring</button>
            <button class="close-modal" onclick="closeClaimModal()">Close</button>
        </div>
    </div>

    <!-- Student Verification Modal -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon" style="background: linear-gradient(135deg, var(--warning), #ff9800);">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3>Student Verification</h3>
            <p>Enter your university email to verify and unlock extra 10% discount!</p>
            <input type="email" id="studentEmail" placeholder="your-name@university.lk" style="width: 100%; padding: 12px; border-radius: 40px; border: 2px solid var(--gray-200); margin-bottom: 16px;">
            <button class="modal-btn" onclick="verifyStudentEmail()" style="width: 100%;">Verify & Get Code</button>
            <button class="close-modal" onclick="closeStudentModal()" style="margin-top: 16px;">Skip for now</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© 2024 SmartTour | Exclusive offers for students and young travelers</p>
            <p style="margin-top: 8px; font-size: 0.8rem;">✨ Valid student ID required for verification. Terms & conditions apply.</p>
        </div>
    </footer>

    <script>
        let currentOffer = '';

        function claimOffer(offerName, discount) {
            currentOffer = offerName;
            const message = document.getElementById('claimMessage');
            message.innerHTML = `You have successfully claimed <strong>${discount}</strong> on <strong>${offerName}</strong>! 🎉`;
            document.getElementById('claimModal').style.display = 'flex';
        }

        function closeClaimModal() {
            document.getElementById('claimModal').style.display = 'none';
        }

        function verifyStudent() {
            document.getElementById('studentModal').style.display = 'flex';
        }

        function closeStudentModal() {
            document.getElementById('studentModal').style.display = 'none';
        }

        function verifyStudentEmail() {
            const email = document.getElementById('studentEmail').value;
            if (email && email.includes('@')) {
                alert(`🎓 Verification email sent to ${email}\n\nUse code: STUDENT10 for extra 10% off!`);
                closeStudentModal();
            } else {
                alert('Please enter a valid student email address');
            }
        }

        function subscribeNewsletter(event) {
            event.preventDefault();
            const email = event.target.querySelector('input').value;
            if (email) {
                alert(`📧 Thanks for subscribing! Exclusive offers will be sent to ${email}`);
                event.target.reset();
            }
        }

        // Countdown timers (simulated)
        function updateCountdowns() {
            const countdowns = document.querySelectorAll('.countdown');
            countdowns.forEach(countdown => {
                const hours = countdown.querySelectorAll('.countdown-number')[0];
                const mins = countdown.querySelectorAll('.countdown-number')[1];
                const secs = countdown.querySelectorAll('.countdown-number')[2];
                
                if (hours && mins && secs) {
                    let h = parseInt(hours.textContent);
                    let m = parseInt(mins.textContent);
                    let s = parseInt(secs.textContent);
                    
                    s--;
                    if (s < 0) {
                        s = 59;
                        m--;
                        if (m < 0) {
                            m = 59;
                            h--;
                            if (h < 0) {
                                h = 0;
                                m = 0;
                                s = 0;
                            }
                        }
                    }
                    
                    hours.textContent = h;
                    mins.textContent = m;
                    secs.textContent = s;
                }
            });
        }

        setInterval(updateCountdowns, 1000);

        // Close modals when clicking outside
        window.onclick = function(event) {
            const claimModal = document.getElementById('claimModal');
            const studentModal = document.getElementById('studentModal');
            if (event.target == claimModal) closeClaimModal();
            if (event.target == studentModal) closeStudentModal();
        }
    </script>
</body>
</html>