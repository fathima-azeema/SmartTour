<?php
// student-dashboard.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

// Get user data
$user_name = $_SESSION['first_name'] ?? 'Student';
$user_email = $_SESSION['email'] ?? 'student@smarttour.com';
$user_id = $_SESSION['user_id'];


$profile_data = [
    'full_name' => $user_name,
    'email' => $user_email,
    'phone' => $_SESSION['phone'] ?? '+94 77 XXX XXXX',
    'university' => $_SESSION['university'] ?? 'Not set',
    'major' => $_SESSION['major'] ?? 'Not set',
    'year' => $_SESSION['year'] ?? 'Not set',
    'bio' => $_SESSION['bio'] ?? 'Tourism student eager to learn and grow.',
    'skills' => $_SESSION['skills'] ?? ['Communication'],
    'languages' => $_SESSION['languages'] ?? ['English']
];
//imporant : get jobs dataaa in database
$applied_jobs = [];
$job_stats = [
    'applied' => 0,
    'interviews' => 0,
    'offers' => 0,
    'saved' => 0
];

$conn = getDBConnection();

// Check if job_applications : coonnect
$table_check = $conn->query("SHOW TABLES LIKE 'job_applications'");
if ($table_check->num_rows > 0) {
    $sql = "SELECT * FROM job_applications WHERE user_id = ? OR applicant_email = ? ORDER BY applied_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $applied_jobs[] = $row;
    }
    $stmt->close();
    
    // Update stats
    $job_stats['applied'] = count($applied_jobs);
    foreach ($applied_jobs as $job) {
        if ($job['status'] == 'reviewed') $job_stats['interviews']++;
        elseif ($job['status'] == 'shortlisted') $job_stats['offers']++;
    }
}

//entrol course
if (!isset($_SESSION['enrolled_courses'])) {
    $_SESSION['enrolled_courses'] = [];
}

$enrolled_courses = $_SESSION['enrolled_courses'];
$learning_progress = [
    'completed' => 0,
    'in_progress' => 0,
    'total_courses' => count($enrolled_courses),
    'certificates' => 0
];

foreach ($enrolled_courses as $course) {
    if ($course['progress'] >= 100) {
        $learning_progress['completed']++;
        $learning_progress['certificates']++;
    } else {
        $learning_progress['in_progress']++;
    }
}

// Sample course data
$all_courses = [
    ['id' => 1, 'name' => 'Introduction to Tourism', 'category' => 'Beginner', 'students' => 1245, 'rating' => 4.8, 'duration' => '4 hours', 'instructor' => 'Dr. Sarah Miller'],
    ['id' => 2, 'name' => 'Hospitality Management', 'category' => 'Intermediate', 'students' => 892, 'rating' => 4.7, 'duration' => '6 hours', 'instructor' => 'Prof. James Wilson'],
    ['id' => 3, 'name' => 'Sustainable Tourism', 'category' => 'Advanced', 'students' => 567, 'rating' => 4.9, 'duration' => '8 hours', 'instructor' => 'Dr. Emma Green'],
    ['id' => 4, 'name' => 'Tour Guide Training', 'category' => 'Professional', 'students' => 734, 'rating' => 4.8, 'duration' => '10 hours', 'instructor' => 'Mr. David Lee']
];

// Get enrolled course details
$enrolled_course_details = [];
foreach ($enrolled_courses as $ec) {
    foreach ($all_courses as $ac) {
        if ($ac['id'] == $ec['id']) {
            $ac['progress'] = $ec['progress'] ?? 0;
            $enrolled_course_details[] = $ac;
            break;
        }
    }
}

$learning_resources = $enrolled_course_details;

// Upcoming events
$upcoming_events = [
    ['id' => 1, 'title' => 'Tourism Career Fair', 'date' => 'Dec 15, 2024', 'time' => '10:00 AM', 'location' => 'Online', 'speaker' => 'Industry Experts'],
    ['id' => 2, 'title' => 'Networking with Industry Pros', 'date' => 'Dec 20, 2024', 'time' => '2:00 PM', 'location' => 'Colombo', 'speaker' => 'Travel Leaders']
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/student-dashboard.css">

</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>SmartTour</span>
                </div>
                <div class="user-profile">
                    <div class="avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($user_name); ?></h3>
                        <span class="user-role">Student</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="student-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="#profile"><i class="fas fa-user-circle"></i> My Profile</a></li>
                    <li><a href="student-jobs.php"><i class="fas fa-briefcase"></i> Job Applications</a></li>
                    <li><a href="student-courses.php"><i class="fas fa-book-open"></i> Courses</a></li>
                    <li><a href="#learning"><i class="fas fa-graduation-cap"></i> My Learning</a></li>
                    <li><a href="#events"><i class="fas fa-calendar-alt"></i> Events</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="index.php"><i class="fas fa-globe"></i> Home</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! <span class="welcome-emoji">👩‍🎓</span></h1>
                    <p>Ready to explore opportunities in the tourism industry?</p>
                </div>
                <div class="header-actions">
                    <button class="btn-notification" onclick="showNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="date-display">
                        <i class="fas fa-calendar-day"></i>
                        <span><?php echo date('l, F j, Y'); ?></span>
                    </div>
                </div>
            </header>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $job_stats['applied']; ?></h3>
                        <p>Jobs Applied</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $learning_progress['completed']; ?></h3>
                        <p>Courses Completed</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $learning_progress['certificates']; ?></h3>
                        <p>Certificates Earned</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-content">
                        <h3><?php echo count($upcoming_events); ?></h3>
                        <p>Upcoming Events</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections">
                <!-- Profile Section -->
                <section id="profile" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user-circle"></i> My Profile</h2>
                        <button class="btn-primary" onclick="openEditProfileModal()"><i class="fas fa-edit"></i> Edit Profile</button>
                    </div>
                    <div class="profile-grid">
                        <div class="profile-card">
                            <h3>Personal Information</h3>
                            <div class="info-row">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['full_name']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['email']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Phone</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['phone']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">University</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['university']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Major</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['major']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Year</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['year']); ?></div>
                            </div>
                        </div>
                        <div class="profile-card">
                            <h3>Skills & Languages</h3>
                            <div class="info-row">
                                <div class="info-label">Skills</div>
                                <div class="info-value">
                                    <div class="skills-list">
                                        <?php if(!empty($profile_data['skills']) && $profile_data['skills'][0] != 'Communication'): ?>
                                            <?php foreach($profile_data['skills'] as $skill): ?>
                                                <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="skill-tag" style="background: var(--gray-200); color: var(--gray-500);">No skills added yet</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Languages</div>
                                <div class="info-value">
                                    <div class="skills-list">
                                        <?php if(!empty($profile_data['languages']) && $profile_data['languages'][0] != 'English'): ?>
                                            <?php foreach($profile_data['languages'] as $lang): ?>
                                                <span class="skill-tag"><?php echo htmlspecialchars($lang); ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="skill-tag" style="background: var(--gray-200); color: var(--gray-500);">No languages added yet</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Bio</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile_data['bio']); ?></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ============================================================
                     ✅ APPLIED JOBS SECTION
                     ============================================================ -->
                <section id="jobs" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-briefcase"></i> My Job Applications</h2>
                        <a href="student-jobs.php" class="btn-primary">Browse More Jobs <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <?php if (empty($applied_jobs)): ?>
                        <div class="empty-state">
                            <i class="fas fa-paper-plane"></i>
                            <h3>No Applications Yet</h3>
                            <p>Start applying to jobs and track your applications here!</p>
                            <a href="student-jobs.php" class="btn-primary" style="margin-top: 16px;">Browse Jobs</a>
                        </div>
                    <?php else: ?>
                        <div class="jobs-list">
                            <?php foreach($applied_jobs as $job): ?>
                            <div class="job-item">
                                <div class="job-title"><?php echo htmlspecialchars($job['job_title']); ?></div>
                                <div class="company"><?php echo htmlspecialchars($job['company']); ?></div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                    <span style="font-size: 0.8rem; color: var(--gray-500);">
                                        Applied: <?php echo date('M d, Y', strtotime($job['applied_at'])); ?>
                                    </span>
                                    <span class="job-status-badge <?php echo $job['status']; ?>">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- ============================================================
                     ✅ ENROLLED COURSES SECTION
                     ============================================================ -->
                <section id="learning" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-graduation-cap"></i> My Enrolled Courses</h2>
                        <a href="student-courses.php" class="btn-primary">View All Courses <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <?php if (empty($enrolled_course_details)): ?>
                        <div class="empty-state">
                            <i class="fas fa-book-open"></i>
                            <h3>No Courses Enrolled Yet</h3>
                            <p>Start exploring courses to build your skills!</p>
                            <a href="student-courses.php" class="btn-primary" style="margin-top: 16px;">Browse Courses</a>
                        </div>
                    <?php else: ?>
                        <div class="courses-grid">
                            <?php foreach($enrolled_course_details as $course): ?>
                            <div class="course-card">
                                <div class="course-icon"><i class="fas fa-graduation-cap"></i></div>
                                <h3><?php echo $course['name']; ?></h3>
                                <div style="font-size: 0.8rem; color: var(--gray-500);"><?php echo $course['category']; ?></div>
                                <div class="course-progress-bar">
                                    <div class="progress" style="width: <?php echo $course['progress']; ?>%"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                    <span><?php echo $course['progress']; ?>%</span>
                                    <span class="course-status <?php echo $course['progress'] >= 100 ? 'completed' : 'in-progress'; ?>">
                                        <?php echo $course['progress'] >= 100 ? '✅ Completed' : '🔄 In Progress'; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Quick Actions -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-rocket"></i> Quick Actions</h2>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <a href="student-courses.php" class="btn-primary" style="justify-content: center; padding: 16px;">
                            <i class="fas fa-book-open"></i> Browse Courses
                        </a>
                        <a href="student-jobs.php" class="btn-primary" style="justify-content: center; padding: 16px; background: linear-gradient(135deg, var(--success), #36b37e);">
                            <i class="fas fa-briefcase"></i> Find Jobs
                        </a>
                    </div>
                </div>

                <!-- Upcoming Events Section -->
                <section id="events" class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-alt"></i> Upcoming Events</h2>
                        <button class="btn-primary" onclick="viewAllEvents()">Calendar View <i class="fas fa-calendar"></i></button>
                    </div>
                    <div class="events-grid">
                        <?php foreach($upcoming_events as $event): ?>
                        <div class="event-card">
                            <div class="event-date">
                                <strong><?php echo date('d', strtotime($event['date'])); ?></strong>
                                <span><?php echo date('M', strtotime($event['date'])); ?></span>
                            </div>
                            <div class="event-details">
                                <h4><?php echo $event['title']; ?></h4>
                                <p><i class="fas fa-clock"></i> <?php echo $event['time']; ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo $event['location']; ?></p>
                                <p><i class="fas fa-user"></i> Speaker: <?php echo $event['speaker']; ?></p>
                                <button class="btn-primary" style="margin-top: 10px; padding: 6px 12px; font-size: 0.8rem;" onclick="registerEvent(<?php echo $event['id']; ?>)">Register Now</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <button class="close-modal" onclick="closeModal('editProfileModal')">&times;</button>
            </div>
            <form id="profileForm" onsubmit="saveProfile(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($profile_data['full_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($profile_data['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label>University</label>
                        <input type="text" name="university" value="<?php echo htmlspecialchars($profile_data['university']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Major</label>
                        <input type="text" name="major" value="<?php echo htmlspecialchars($profile_data['major']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" name="year" value="<?php echo htmlspecialchars($profile_data['year']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="3"><?php echo htmlspecialchars($profile_data['bio']); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editProfileModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function saveProfile(event) {
            event.preventDefault();
            alert('✅ Profile updated successfully!');
            closeModal('editProfileModal');
        }

        // Other functions
        function showNotifications() {
            alert('🔔 You have 3 new notifications:\n1. New course available\n2. Job application update\n3. Upcoming webinar reminder');
        }

        function viewAllEvents() {
            alert('📅 Calendar view coming soon!');
        }

        function registerEvent(id) {
            alert(`✅ Registered for event #${id}! Check your email for confirmation.`);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editProfileModal');
            if (event.target == modal) {
                closeModal('editProfileModal');
            }
        }
    </script>
</body>
</html>