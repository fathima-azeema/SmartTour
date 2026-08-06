<?php
// my-tours.php - Guide Tour Management
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'guide') {
    header("Location: login.php");
    exit();
}

$guide_name = $_SESSION['first_name'] ?? 'Guide';

// Sample tours data (in real app, fetch from database)
$default_tours = [
    [
        'id' => 1,
        'title' => 'Cultural Triangle Expedition',
        'description' => 'Explore the ancient cities of Anuradhapura, Polonnaruwa, and the rock fortress of Sigiriya. Perfect for history lovers.',
        'location' => 'Cultural Triangle',
        'duration' => '3 Days',
        'price' => 45000,
        'max_participants' => 12,
        'status' => 'active',
        'bookings' => 8,
        'image' => 'https://images.unsplash.com/photo-1598974357801-cbca100e65d3?auto=format&fit=crop&w=400'
    ],
    [
        'id' => 2,
        'title' => 'Hill Country Adventure',
        'description' => 'Trek through tea plantations, visit waterfalls, and enjoy breathtaking views in Nuwara Eliya and Ella.',
        'location' => 'Hill Country',
        'duration' => '2 Days',
        'price' => 35000,
        'max_participants' => 10,
        'status' => 'upcoming',
        'bookings' => 3,
        'image' => 'https://images.unsplash.com/photo-1580519542036-c47de6196ba5?auto=format&fit=crop&w=400'
    ],
    [
        'id' => 3,
        'title' => 'Southern Beach Getaway',
        'description' => 'Relax on pristine beaches, visit Galle Fort, and enjoy whale watching in Mirissa.',
        'location' => 'Southern Coast',
        'duration' => '4 Days',
        'price' => 55000,
        'max_participants' => 15,
        'status' => 'completed',
        'bookings' => 12,
        'image' => 'https://images.unsplash.com/photo-1559666647-215d6616eaa3?auto=format&fit=crop&w=400'
    ],
    [
        'id' => 4,
        'title' => 'Wildlife Safari',
        'description' => 'Experience the thrill of spotting leopards, elephants, and exotic birds in Yala National Park.',
        'location' => 'Yala',
        'duration' => '2 Days',
        'price' => 40000,
        'max_participants' => 8,
        'status' => 'active',
        'bookings' => 5,
        'image' => 'https://images.unsplash.com/photo-1575363374538-03146d7a7322?auto=format&fit=crop&w=400'
    ]
];


if (!isset($_SESSION['guide_tours'])) {
    $_SESSION['guide_tours'] = $default_tours;
}
$tours = $_SESSION['guide_tours'];

// Handle actions (create, update, delete) via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $new_tour = [
            'id' => count($tours) + 1,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'location' => $_POST['location'],
            'duration' => $_POST['duration'],
            'price' => (int)$_POST['price'],
            'max_participants' => (int)$_POST['max_participants'],
            'status' => $_POST['status'],
            'bookings' => 0,
            'image' => $_POST['image'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400'
        ];
        $tours[] = $new_tour;
        $_SESSION['guide_tours'] = $tours;
        header("Location: my-tours.php?success=created");
        exit();
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        foreach ($tours as &$tour) {
            if ($tour['id'] === $id) {
                $tour['title'] = $_POST['title'];
                $tour['description'] = $_POST['description'];
                $tour['location'] = $_POST['location'];
                $tour['duration'] = $_POST['duration'];
                $tour['price'] = (int)$_POST['price'];
                $tour['max_participants'] = (int)$_POST['max_participants'];
                $tour['status'] = $_POST['status'];
                $tour['image'] = $_POST['image'] ?: $tour['image'];
                break;
            }
        }
        $_SESSION['guide_tours'] = $tours;
        header("Location: my-tours.php?success=updated");
        exit();
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $tours = array_filter($tours, function($tour) use ($id) {
            return $tour['id'] !== $id;
        });
        $_SESSION['guide_tours'] = $tours;
        header("Location: my-tours.php?success=deleted");
        exit();
    }
}

$success_msg = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tours - SmartTour Guide</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/my-tours.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1>🗺️ <span>My Tours</span></h1>
                <p style="color: var(--text-secondary); font-size: 0.95rem;">Manage your tour packages and track bookings</p>
            </div>
            <div class="header-actions">
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
                <a href="guide-dashboard.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <button class="add-btn" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add New Tour
                </button>
            </div>
        </div>

        <!-- Stats -->
        <?php
        $total = count($tours);
        $active = count(array_filter($tours, fn($t) => $t['status'] == 'active'));
        $upcoming = count(array_filter($tours, fn($t) => $t['status'] == 'upcoming'));
        $completed = count(array_filter($tours, fn($t) => $t['status'] == 'completed'));
        $total_bookings = array_sum(array_column($tours, 'bookings'));
        ?>
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number" id="totalTours"><?php echo $total; ?></div>
                <div class="stat-label">Total Tours</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="activeTours"><?php echo $active; ?></div>
                <div class="stat-label">Active</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="upcomingTours"><?php echo $upcoming; ?></div>
                <div class="stat-label">Upcoming</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="totalBookings"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="search-filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search tours..." oninput="filterTours()">
            </div>
            <div class="filter-group" id="statusFilters">
                <button class="filter-btn active" data-status="all" onclick="filterByStatus('all')">All</button>
                <button class="filter-btn" data-status="active" onclick="filterByStatus('active')">Active</button>
                <button class="filter-btn" data-status="upcoming" onclick="filterByStatus('upcoming')">Upcoming</button>
                <button class="filter-btn" data-status="completed" onclick="filterByStatus('completed')">Completed</button>
            </div>
        </div>

        <!-- Tours Grid -->
        <div class="tours-grid" id="toursGrid">
            <?php if (empty($tours)): ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <i class="fas fa-route" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 16px;"></i>
                    <h3>No Tours Yet</h3>
                    <p style="color: var(--text-secondary);">Create your first tour package to start attracting travelers!</p>
                </div>
            <?php else: foreach ($tours as $tour): ?>
                <div class="tour-card" data-id="<?php echo $tour['id']; ?>" data-status="<?php echo $tour['status']; ?>" data-name="<?php echo strtolower($tour['title']); ?>">
                    <div class="tour-image" style="background-image: url('<?php echo $tour['image']; ?>');">
                        <span class="tour-status-badge <?php echo $tour['status']; ?>"><?php echo ucfirst($tour['status']); ?></span>
                    </div>
                    <div class="tour-content">
                        <div class="tour-title"><?php echo htmlspecialchars($tour['title']); ?></div>
                        <div class="tour-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($tour['location']); ?></div>
                        <p class="tour-description"><?php echo htmlspecialchars($tour['description']); ?></p>
                        <div class="tour-meta">
                            <span><i class="fas fa-clock"></i> <?php echo $tour['duration']; ?></span>
                            <span><i class="fas fa-users"></i> Max <?php echo $tour['max_participants']; ?> pax</span>
                            <span><i class="fas fa-tag"></i> LKR <?php echo number_format($tour['price']); ?></span>
                            <span><i class="fas fa-calendar-check"></i> <?php echo $tour['bookings']; ?> bookings</span>
                        </div>
                        <div class="tour-actions">
                            <button class="btn-edit" onclick="openEditModal(<?php echo $tour['id']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-delete" onclick="confirmDelete(<?php echo $tour['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <button class="btn-bookings" onclick="viewBookings(<?php echo $tour['id']; ?>)">
                                <i class="fas fa-eye"></i> Bookings
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="tourModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-plus"></i> Add New Tour</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="tourForm" method="POST" action="my-tours.php">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="formId" value="">
                <div class="form-group">
                    <label>Tour Title</label>
                    <input type="text" name="title" id="tourTitle" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="tourDescription" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="tourLocation">
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" id="tourDuration" placeholder="e.g. 3 Days">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (LKR)</label>
                        <input type="number" name="price" id="tourPrice" required>
                    </div>
                    <div class="form-group">
                        <label>Max Participants</label>
                        <input type="number" name="max_participants" id="tourMaxParticipants" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="tourStatus">
                        <option value="active">Active</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" id="tourImage" placeholder="https://example.com/image.jpg">
                </div>
                <button type="submit" class="btn-submit" id="formSubmitBtn">Add Tour</button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i> Confirm Delete</h3>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p style="margin-bottom: 20px; color: var(--text-secondary);">Are you sure you want to delete this tour? This action cannot be undone.</p>
            <form method="POST" action="my-tours.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId" value="">
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn-delete-confirm">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Modal -->
    <div id="bookingsModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3><i class="fas fa-users"></i> Tour Bookings</h3>
                <button class="modal-close" onclick="closeBookingsModal()">&times;</button>
            </div>
            <div id="bookingsList" style="max-height: 400px; overflow-y: auto;">
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
        let toursData = <?php echo json_encode($tours); ?>;

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
            filterTours();
        }

        function filterTours() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.tour-card');
            let visible = 0;

            cards.forEach(card => {
                const status = card.dataset.status;
                const name = card.dataset.name;
                const matchStatus = currentStatus === 'all' || status === currentStatus;
                const matchSearch = name.includes(search);
                const isVisible = matchStatus && matchSearch;

                card.classList.toggle('hidden', !isVisible);
                if (isVisible) visible++;
            });
        }

        // ============================================================
        // ADD/EDIT MODAL
        // ============================================================
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Add New Tour';
            document.getElementById('formAction').value = 'add';
            document.getElementById('formId').value = '';
            document.getElementById('tourForm').reset();
            document.getElementById('tourModal').classList.add('active');
        }

        function openEditModal(id) {
            const tour = toursData.find(t => t.id === id);
            if (!tour) return;

            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Tour';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('formId').value = tour.id;
            document.getElementById('tourTitle').value = tour.title;
            document.getElementById('tourDescription').value = tour.description || '';
            document.getElementById('tourLocation').value = tour.location || '';
            document.getElementById('tourDuration').value = tour.duration || '';
            document.getElementById('tourPrice').value = tour.price;
            document.getElementById('tourMaxParticipants').value = tour.max_participants;
            document.getElementById('tourStatus').value = tour.status;
            document.getElementById('tourImage').value = tour.image || '';

            document.getElementById('tourModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('tourModal').classList.remove('active');
        }

        // ============================================================
        // DELETE
        // ============================================================
        function confirmDelete(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // ============================================================
        // BOOKINGS
        // ============================================================
        function viewBookings(id) {
            const tour = toursData.find(t => t.id === id);
            if (!tour) return;

            // Sample booking data (in real app, fetch from DB)
            const sampleBookings = [
                { client: 'John Doe', date: '2024-01-15', participants: 2, status: 'confirmed' },
                { client: 'Jane Smith', date: '2024-01-20', participants: 3, status: 'pending' },
                { client: 'Bob Johnson', date: '2024-01-25', participants: 1, status: 'confirmed' }
            ];

            let html = `<div style="margin-bottom: 16px;"><strong>${tour.title}</strong> - ${tour.bookings} bookings</div>`;
            if (tour.bookings === 0) {
                html += '<p style="color: var(--text-secondary);">No bookings yet.</p>';
            } else {
                html += `<div class="app-list">`;
                sampleBookings.forEach(b => {
                    html += `
                        <div class="app-item" style="display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid var(--border);">
                            <div>
                                <strong>${b.client}</strong><br>
                                <small>${b.date} · ${b.participants} pax</small>
                            </div>
                            <span class="app-status" style="padding: 2px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: ${b.status === 'confirmed' ? '#dbeafe' : '#fef3c7'}; color: ${b.status === 'confirmed' ? '#2563eb' : '#d97706'};">${b.status}</span>
                        </div>
                    `;
                });
                html += '</div>';
            }

            document.getElementById('bookingsList').innerHTML = html;
            document.getElementById('bookingsModal').classList.add('active');
        }

        function closeBookingsModal() {
            document.getElementById('bookingsModal').classList.remove('active');
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
        // CLOSE MODALS ON OUTSIDE CLICK
        // ============================================================
        window.onclick = function(event) {
            const modals = ['tourModal', 'deleteModal', 'bookingsModal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (event.target == modal) {
                    modal.classList.remove('active');
                }
            });
        }

        // ============================================================
        // SUCCESS TOAST FROM URL
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            applyTheme();

            const success = new URLSearchParams(window.location.search).get('success');
            if (success === 'created') showToast('Tour created successfully! 🎉');
            else if (success === 'updated') showToast('Tour updated successfully! ✅');
            else if (success === 'deleted') showToast('Tour deleted successfully! 🗑️');
        });
    </script>
</body>
</html>