<?php
// profile.php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? 'tourist';
$user_name = $_SESSION['first_name'] ?? 'User';

// Get database connection
$conn = getDBConnection();

// First, check what columns exist in the users table
$columns = array();
$col_result = $conn->query("SHOW COLUMNS FROM users");
if ($col_result) {
    while ($col = $col_result->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
}

// Fetch user data - with proper error handling
$user = null;
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
}

// If user not found, create a default array to prevent null errors
if ($user === null) {
    $user = [
        'id' => $user_id,
        'first_name' => $user_name,
        'last_name' => '',
        'email' => $_SESSION['email'] ?? '',
        'phone' => '',
        'bio' => '',
        'university' => '',
        'major' => '',
        'graduation_year' => '',
        'experience' => '',
        'specialization' => '',
        'languages' => '',
        'certification' => '',
        'password' => '',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Handle profile update
$update_success = false;
$update_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $bio = isset($_POST['bio']) ? sanitize($_POST['bio']) : '';
    
    // Additional fields based on user type
    $university = isset($_POST['university']) ? sanitize($_POST['university']) : '';
    $major = isset($_POST['major']) ? sanitize($_POST['major']) : '';
    $graduation_year = isset($_POST['graduation_year']) ? sanitize($_POST['graduation_year']) : '';
    $experience = isset($_POST['experience']) ? sanitize($_POST['experience']) : '';
    $specialization = isset($_POST['specialization']) ? sanitize($_POST['specialization']) : '';
    $languages = isset($_POST['languages']) ? sanitize($_POST['languages']) : '';
    $certification = isset($_POST['certification']) ? sanitize($_POST['certification']) : '';
    
    // Build update query dynamically based on existing columns
    $update_fields = array();
    $params = array();
    $types = "";
    
    // Always update these fields (check if columns exist)
    $field_mapping = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone' => $phone,
        'bio' => $bio
    ];
    
    foreach ($field_mapping as $field => $value) {
        if (in_array($field, $columns)) {
            $update_fields[] = "$field = ?";
            $params[] = $value;
            $types .= "s";
        }
    }
    
    // Student specific fields
    if ($user_type == 'student') {
        $student_fields = ['university', 'major', 'graduation_year'];
        $student_values = [$university, $major, $graduation_year];
        foreach ($student_fields as $index => $field) {
            if (in_array($field, $columns)) {
                $update_fields[] = "$field = ?";
                $params[] = $student_values[$index];
                $types .= "s";
            }
        }
    }
    
    // Guide specific fields
    if ($user_type == 'guide') {
        $guide_fields = ['experience', 'specialization', 'languages', 'certification'];
        $guide_values = [$experience, $specialization, $languages, $certification];
        foreach ($guide_fields as $index => $field) {
            if (in_array($field, $columns)) {
                $update_fields[] = "$field = ?";
                $params[] = $guide_values[$index];
                $types .= "s";
            }
        }
    }
    
    // Only proceed if there are fields to update
    if (!empty($update_fields)) {
        $update_sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";
        
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param($types, ...$params);
            if ($update_stmt->execute()) {
                $update_success = true;
                $_SESSION['first_name'] = $first_name;
                // Refresh user data
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['phone'] = $phone;
                $user['bio'] = $bio;
            } else {
                $update_error = "Failed to update profile: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $update_error = "Failed to prepare update statement.";
        }
    } else {
        $update_error = "No fields to update.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (isset($user['password']) && password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $pass_sql = "UPDATE users SET password = ? WHERE id = ?";
                $pass_stmt = $conn->prepare($pass_sql);
                if ($pass_stmt) {
                    $pass_stmt->bind_param("si", $hashed_password, $user_id);
                    if ($pass_stmt->execute()) {
                        $update_success = true;
                    } else {
                        $update_error = "Failed to update password.";
                    }
                    $pass_stmt->close();
                }
            } else {
                $update_error = "Password must be at least 6 characters.";
            }
        } else {
            $update_error = "New passwords do not match.";
        }
    } else {
        $update_error = "Current password is incorrect.";
    }
}

$conn->close();

// Sample data for user type specific fields
$student_data = [
    'university' => $user['university'] ?? 'University of Colombo',
    'major' => $user['major'] ?? 'Tourism Management',
    'graduation_year' => $user['graduation_year'] ?? '2025',
    'student_id' => 'STU2024' . $user_id
];

$guide_data = [
    'experience' => $user['experience'] ?? '5+ years',
    'specialization' => $user['specialization'] ?? 'Cultural Heritage, Adventure Tours',
    'languages' => $user['languages'] ?? 'English, Sinhala, Tamil',
    'certification' => $user['certification'] ?? 'Certified Tour Guide - Level 3',
    'rating' => 4.8,
    'total_tours' => 127,
    'total_clients' => 342
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SmartTour</title>
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
            max-width: 1200px;
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
            font-size: 2.5rem;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .hero p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        /* Profile Container */
        .profile-container {
            margin-top: -40px;
            margin-bottom: 60px;
            position: relative;
            z-index: 10;
        }

        .profile-card {
            background: white;
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .profile-header {
            background: linear-gradient(135deg, rgba(67,97,238,0.1), rgba(114,9,183,0.05));
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 10px 30px rgba(67,97,238,0.3);
            border: 4px solid white;
        }

        .profile-role {
            display: inline-block;
            padding: 5px 15px;
            background: rgba(67,97,238,0.1);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
            margin-top: 10px;
        }

        /* Tabs */
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid var(--gray-200);
            padding: 0 30px;
            background: white;
        }

        .tab-btn {
            padding: 16px 24px;
            background: none;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-600);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.active {
            color: var(--primary);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 3px;
        }

        /* Tab Content */
        .tab-content {
            display: none;
            padding: 30px;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--gray-700);
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67,97,238,0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-200);
        }

        .btn-primary {
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }

        .btn-secondary {
            padding: 12px 30px;
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
        }

        /* Stats Cards (for Guide) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--gray-50);
            padding: 20px;
            border-radius: var(--radius-lg);
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        .stat-card i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .stat-card .label {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid var(--success-dark);
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border-left: 4px solid var(--danger);
            color: #721c24;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Verification Badges */
        .verification-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--gray-100);
            border-radius: 40px;
            font-size: 0.85rem;
        }

        .verification-badge.verified {
            background: rgba(76,201,240,0.1);
            color: var(--success-dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .hero h1 { font-size: 1.8rem; }
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .profile-tabs { flex-wrap: wrap; }
            .tab-btn { flex: 1; text-align: center; padding: 12px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
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
                    <a href="dashboard.php" style="text-decoration: none; color: var(--primary);">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>👤 My Profile</h1>
            <p>Manage your personal information and account settings</p>
        </div>
    </section>

    <div class="container">
        <div class="profile-container">
            <div class="profile-card">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <h2><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></h2>
                    <div class="profile-role">
                        <i class="fas fa-<?php echo $user_type == 'tourist' ? 'umbrella-beach' : ($user_type == 'student' ? 'graduation-cap' : 'map-signs'); ?>"></i>
                        <?php echo ucfirst($user_type); ?> Account
                    </div>
                </div>

                <!-- Tabs -->
                <div class="profile-tabs">
                    <button class="tab-btn active" onclick="showTab('personal')">📝 Personal Info</button>
                    <?php if ($user_type == 'student'): ?>
                        <button class="tab-btn" onclick="showTab('education')">🎓 Education</button>
                    <?php elseif ($user_type == 'guide'): ?>
                        <button class="tab-btn" onclick="showTab('professional')">🗺️ Professional Info</button>
                        <button class="tab-btn" onclick="showTab('stats')">📊 Stats</button>
                    <?php endif; ?>
                    <button class="tab-btn" onclick="showTab('security')">🔒 Security</button>
                </div>

                <!-- Alert Messages -->
                <?php if ($update_success): ?>
                    <div class="alert alert-success" style="margin: 20px 30px 0 30px;">
                        <i class="fas fa-check-circle"></i>
                        Profile updated successfully!
                    </div>
                <?php elseif ($update_error): ?>
                    <div class="alert alert-error" style="margin: 20px 30px 0 30px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $update_error; ?>
                    </div>
                <?php endif; ?>

                <!-- Tab: Personal Information -->
                <div id="personalTab" class="tab-content active">
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> First Name</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Last Name</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email Address</label>
                                <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                                <small style="color: var(--gray-500);">Email cannot be changed</small>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-comment"></i> Bio / About Me</label>
                                <textarea name="bio" placeholder="Tell us a little about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Education (For Students Only) -->
                <?php if ($user_type == 'student'): ?>
                <div id="educationTab" class="tab-content">
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-university"></i> University / Institution</label>
                                <input type="text" name="university" value="<?php echo htmlspecialchars($student_data['university']); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-graduation-cap"></i> Major / Field of Study</label>
                                <input type="text" name="major" value="<?php echo htmlspecialchars($student_data['major']); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar"></i> Expected Graduation Year</label>
                                <input type="text" name="graduation_year" value="<?php echo htmlspecialchars($student_data['graduation_year']); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> Student ID</label>
                                <input type="text" value="<?php echo htmlspecialchars($student_data['student_id']); ?>" disabled>
                                <small>Contact support to change Student ID</small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn-primary">
                                <i class="fas fa-save"></i> Save Education Info
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Tab: Professional Info (For Guides Only) -->
                <?php if ($user_type == 'guide'): ?>
                <div id="professionalTab" class="tab-content">
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-briefcase"></i> Years of Experience</label>
                                <input type="text" name="experience" value="<?php echo htmlspecialchars($guide_data['experience']); ?>">
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-tag"></i> Specialization</label>
                                <input type="text" name="specialization" value="<?php echo htmlspecialchars($guide_data['specialization']); ?>">
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-language"></i> Languages Spoken</label>
                                <input type="text" name="languages" value="<?php echo htmlspecialchars($guide_data['languages']); ?>">
                            </div>
                            <div class="form-group full-width">
                                <label><i class="fas fa-certificate"></i> Certifications</label>
                                <input type="text" name="certification" value="<?php echo htmlspecialchars($guide_data['certification']); ?>">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn-primary">
                                <i class="fas fa-save"></i> Save Professional Info
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Stats (For Guides Only) -->
                <div id="statsTab" class="tab-content">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="fas fa-star"></i>
                            <div class="number"><?php echo $guide_data['rating']; ?></div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-route"></i>
                            <div class="number"><?php echo $guide_data['total_tours']; ?></div>
                            <div class="label">Total Tours</div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <div class="number"><?php echo $guide_data['total_clients']; ?></div>
                            <div class="label">Happy Clients</div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-certificate"></i>
                            <div class="number">Verified</div>
                            <div class="label">Status</div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h3 style="margin-bottom: 15px;">Verification Status</h3>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <div class="verification-badge verified">
                                <i class="fas fa-check-circle"></i> Email Verified
                            </div>
                            <div class="verification-badge verified">
                                <i class="fas fa-check-circle"></i> Phone Verified
                            </div>
                            <div class="verification-badge">
                                <i class="fas fa-clock"></i> ID Verification Pending
                            </div>
                            <div class="verification-badge">
                                <i class="fas fa-clock"></i> Background Check
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tab: Security -->
                <div id="securityTab" class="tab-content">
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-lock"></i> Current Password</label>
                                <input type="password" name="current_password" placeholder="Enter your current password" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-key"></i> New Password</label>
                                <input type="password" name="new_password" placeholder="Min. 6 characters" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-check"></i> Confirm New Password</label>
                                <input type="password" name="confirm_password" placeholder="Re-enter new password" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="change_password" class="btn-primary">
                                <i class="fas fa-shield-alt"></i> Change Password
                            </button>
                        </div>
                    </form>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gray-200);">
                        <h3 style="margin-bottom: 15px;">Two-Factor Authentication</h3>
                        <p style="color: var(--gray-600); margin-bottom: 15px;">Add an extra layer of security to your account.</p>
                        <button class="btn-secondary" onclick="alert('2FA coming soon! 🚀')">
                            <i class="fas fa-mobile-alt"></i> Enable 2FA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            if (tabName === 'personal') {
                document.getElementById('personalTab').classList.add('active');
                document.querySelector('.tab-btn').classList.add('active');
            } else if (tabName === 'education') {
                document.getElementById('educationTab').classList.add('active');
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
            } else if (tabName === 'professional') {
                document.getElementById('professionalTab').classList.add('active');
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
            } else if (tabName === 'stats') {
                document.getElementById('statsTab').classList.add('active');
                document.querySelectorAll('.tab-btn')[2].classList.add('active');
            } else if (tabName === 'security') {
                document.getElementById('securityTab').classList.add('active');
                const tabs = document.querySelectorAll('.tab-btn');
                tabs[tabs.length - 1].classList.add('active');
            }
        }
    </script>
</body>
</html>