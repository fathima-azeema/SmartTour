<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'guide') {
    header("Location: login.php");
    exit();
}

$guide_name = $_SESSION['first_name'] ?? 'Guide';

// Sample bookings data (in real app, fetch from database)
$sample_bookings = [
    [
        'id' => 1,
        'tour_id' => 1,
        'tour_title' => 'Cultural Triangle Expedition',
        'client_name' => 'Sarah Johnson',
        'client_email' => 'sarah.j@email.com',
        'client_phone' => '+94 77 123 4567',
        'booking_date' => '2024-12-15',
        'participants' => 2,
        'total_amount' => 90000,
        'status' => 'confirmed',
        'special_requests' => 'Vegetarian meal required',
        'tour_date' => '2024-12-20'
    ],
    [
        'id' => 2,
        'tour_id' => 2,
        'tour_title' => 'Hill Country Adventure',
        'client_name' => 'Mike Chen',
        'client_email' => 'mike.c@email.com',
        'client_phone' => '+94 78 234 5678',
        'booking_date' => '2024-12-16',
        'participants' => 3,
        'total_amount' => 105000,
        'status' => 'pending',
        'special_requests' => '',
        'tour_date' => '2024-12-22'
    ],
    [
        'id' => 3,
        'tour_id' => 4,
        'tour_title' => 'Wildlife Safari',
        'client_name' => 'Emma Wilson',
        'client_email' => 'emma.w@email.com',
        'client_phone' => '+94 79 345 6789',
        'booking_date' => '2024-12-17',
        'participants' => 4,
        'total_amount' => 160000,
        'status' => 'pending',
        'special_requests' => 'Early morning pickup requested',
        'tour_date' => '2024-12-25'
    ],
    [
        'id' => 4,
        'tour_id' => 3,
        'tour_title' => 'Southern Beach Getaway',
        'client_name' => 'Robert Brown',
        'client_email' => 'robert.b@email.com',
        'client_phone' => '+94 70 456 7890',
        'booking_date' => '2024-12-18',
        'participants' => 2,
        'total_amount' => 110000,
        'status' => 'confirmed',
        'special_requests' => '',
        'tour_date' => '2024-12-28'
    ],
    [
        'id' => 5,
        'tour_id' => 1,
        'tour_title' => 'Cultural Triangle Expedition',
        'client_name' => 'Lisa Wong',
        'client_email' => 'lisa.w@email.com',
        'client_phone' => '+94 71 567 8901',
        'booking_date' => '2024-12-19',
        'participants' => 1,
        'total_amount' => 45000,
        'status' => 'cancelled',
        'special_requests' => '',
        'tour_date' => '2025-01-05'
    ]
];

// In real app, fetch from database
if (!isset($_SESSION['guide_bookings'])) {
    $_SESSION['guide_bookings'] = $sample_bookings;
}
$bookings = $_SESSION['guide_bookings'];

// Handle booking actions (confirm, cancel)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $booking_id = (int)($_POST['booking_id'] ?? 0);
    
    foreach ($bookings as &$booking) {
        if ($booking['id'] === $booking_id) {
            if ($action === 'confirm') {
                $booking['status'] = 'confirmed';
            } elseif ($action === 'cancel') {
                $booking['status'] = 'cancelled';
            } elseif ($action === 'complete') {
                $booking['status'] = 'completed';
            }
            break;
        }
    }
    $_SESSION['guide_bookings'] = $bookings;
    header("Location: guide-bookings.php?success=" . ($action === 'confirm' ? 'confirmed' : ($action === 'cancel' ? 'cancelled' : 'completed')));
    exit();
}

$success_msg = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Bookings - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/guide-bookings.css">

</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1>📋 <span>Guide Bookings</span></h1>
                <p style="color: var(--text-secondary); font-size: 0.95rem;">View and manage all your tour bookings</p>
            </div>
            <div class="header-actions">
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
                <a href="guide-dashboard.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Stats -->
        <?php
        $total = count($bookings);
        $pending = count(array_filter($bookings, fn($b) => $b['status'] == 'pending'));
        $confirmed = count(array_filter($bookings, fn($b) => $b['status'] == 'confirmed'));
        $cancelled = count(array_filter($bookings, fn($b) => $b['status'] == 'cancelled'));
        $completed = count(array_filter($bookings, fn($b) => $b['status'] == 'completed'));
        ?>
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number" id="totalBookings"><?php echo $total; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="pendingBookings"><?php echo $pending; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="confirmedBookings"><?php echo $confirmed; ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="completedBookings"><?php echo $completed; ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="search-filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by client or tour..." oninput="filterBookings()">
            </div>
            <div class="filter-group" id="statusFilters">
                <button class="filter-btn active" data-status="all" onclick="filterByStatus('all')">All</button>
                <button class="filter-btn" data-status="pending" onclick="filterByStatus('pending')">Pending</button>
                <button class="filter-btn" data-status="confirmed" onclick="filterByStatus('confirmed')">Confirmed</button>
                <button class="filter-btn" data-status="completed" onclick="filterByStatus('completed')">Completed</button>
                <button class="filter-btn" data-status="cancelled" onclick="filterByStatus('cancelled')">Cancelled</button>
            </div>
        </div>

        <!-- Bookings Grid -->
        <div class="bookings-grid" id="bookingsGrid">
            <?php if (empty($bookings)): ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <i class="fas fa-calendar-check" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 16px;"></i>
                    <h3>No Bookings Yet</h3>
                    <p style="color: var(--text-secondary);">Bookings will appear here once travelers request your tours.</p>
                </div>
            <?php else: foreach ($bookings as $booking): ?>
                <div class="booking-card" data-id="<?php echo $booking['id']; ?>" data-status="<?php echo $booking['status']; ?>" data-search="<?php echo strtolower($booking['client_name'] . ' ' . $booking['tour_title']); ?>">
                    <div class="booking-header">
                        <h3><?php echo htmlspecialchars($booking['tour_title']); ?></h3>
                        <span class="booking-status-badge <?php echo $booking['status']; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>

                    <div class="client-info">
                        <div class="name"><?php echo htmlspecialchars($booking['client_name']); ?></div>
                        <div class="email"><?php echo htmlspecialchars($booking['client_email']); ?></div>
                    </div>

                    <div class="booking-details">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($booking['tour_date'])); ?></span>
                        <span><i class="fas fa-users"></i> <?php echo $booking['participants']; ?> pax</span>
                        <span><i class="fas fa-tag"></i> LKR <?php echo number_format($booking['total_amount']); ?></span>
                    </div>

                    <div class="booking-actions">
                        <button class="btn-action btn-details" onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                            <i class="fas fa-eye"></i> Details
                        </button>
                        <?php if ($booking['status'] == 'pending'): ?>
                            <form method="POST" action="guide-bookings.php" style="display: inline;">
                                <input type="hidden" name="action" value="confirm">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-action btn-confirm">
                                    <i class="fas fa-check"></i> Confirm
                                </button>
                            </form>
                            <form method="POST" action="guide-bookings.php" style="display: inline;">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Cancel this booking?')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        <?php elseif ($booking['status'] == 'confirmed'): ?>
                            <form method="POST" action="guide-bookings.php" style="display: inline;">
                                <input type="hidden" name="action" value="complete">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-action btn-complete">
                                    <i class="fas fa-check-double"></i> Complete
                                </button>
                            </form>
                            <form method="POST" action="guide-bookings.php" style="display: inline;">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Cancel this confirmed booking?')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        <?php endif; ?>
                        <button class="btn-action btn-message" onclick="messageClient('<?php echo $booking['client_name']; ?>', '<?php echo $booking['client_email']; ?>')">
                            <i class="fas fa-envelope"></i> Message
                        </button>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle"></i> Booking Details</h3>
                <button class="modal-close" onclick="closeDetailsModal()">&times;</button>
            </div>
            <div id="detailsBody">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ============================================================
        // STATE
        // ============================================================
        let isDark = localStorage.getItem('theme') === 'dark';
        let currentStatus = 'all';
        let bookingsData = <?php echo json_encode($bookings); ?>;

        // ============================================================
        // THEME
        // ============================================================
        function toggleTheme() {
            isDark = !isDark;
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            applyTheme();
        }

        function applyTheme() {
            if (isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.getElementById('themeIcon').className = 'fas fa-sun';
            } else {
                document.documentElement.removeAttribute('data-theme');
                document.getElementById('themeIcon').className = 'fas fa-moon';
            }
        }

        // ============================================================
        // FILTERS
        // ============================================================
        function filterByStatus(status) {
            currentStatus = status;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.status === status);
            });
            filterBookings();
        }

        function filterBookings() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.booking-card');
            let visible = 0;

            cards.forEach(card => {
                const status = card.dataset.status;
                const searchText = card.dataset.search;
                const matchStatus = currentStatus === 'all' || status === currentStatus;
                const matchSearch = searchText.includes(search);
                const isVisible = matchStatus && matchSearch;

                card.classList.toggle('hidden', !isVisible);
                if (isVisible) visible++;
            });
        }

        // ============================================================
        // VIEW BOOKING DETAILS
        // ============================================================
        function viewBookingDetails(id) {
            const booking = bookingsData.find(b => b.id === id);
            if (!booking) return;

            const statusColors = {
                pending: '#f59e0b',
                confirmed: '#2563eb',
                completed: '#16a34a',
                cancelled: '#dc2626'
            };

            document.getElementById('detailsBody').innerHTML = `
                <div style="margin-bottom: 16px;">
                    <span class="booking-status-badge ${booking.status}" style="font-size: 0.85rem; padding: 6px 18px;">
                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Tour</span>
                    <span class="detail-value">${booking.tour_title}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Client Name</span>
                    <span class="detail-value">${booking.client_name}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">${booking.client_email}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone</span>
                    <span class="detail-value">${booking.client_phone}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tour Date</span>
                    <span class="detail-value">${new Date(booking.tour_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Participants</span>
                    <span class="detail-value">${booking.participants} pax</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value">LKR ${booking.total_amount.toLocaleString()}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Booking Date</span>
                    <span class="detail-value">${new Date(booking.booking_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</span>
                </div>
                ${booking.special_requests ? `
                    <div class="detail-row">
                        <span class="detail-label">Special Requests</span>
                        <span class="detail-value">${booking.special_requests}</span>
                    </div>
                ` : ''}
            `;

            document.getElementById('detailsModal').classList.add('active');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.remove('active');
        }

        // ============================================================
        // MESSAGE CLIENT
        // ============================================================
        function messageClient(name, email) {
            const subject = encodeURIComponent('SmartTour Booking Inquiry');
            const body = encodeURIComponent(`Dear ${name},\n\nThank you for booking with SmartTour. I'm reaching out regarding your upcoming tour. Please let me know if you have any questions!\n\nBest regards,\nGuide Team`);
            window.open(`mailto:${email}?subject=${subject}&body=${body}`, '_blank');
        }

        // ============================================================
        // TOAST
        // ============================================================
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ============================================================
        // CLOSE MODAL ON OUTSIDE CLICK
        // ============================================================
        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target == modal) {
                closeDetailsModal();
            }
        }

        // ============================================================
        // SUCCESS TOAST FROM URL
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            applyTheme();

            const success = new URLSearchParams(window.location.search).get('success');
            if (success === 'confirmed') showToast('Booking confirmed successfully! ✅');
            else if (success === 'cancelled') showToast('Booking cancelled successfully! ❌');
            else if (success === 'completed') showToast('Booking marked as completed! 🎉');
        });
    </script>
</body>
</html>