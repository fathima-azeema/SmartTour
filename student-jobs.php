<?php
// job-opportunities.php
session_start();
require_once 'config.php';

// Check if user is logged in
$is_logged_in = isLoggedIn();  // ✅ Using your function
$user_id = getCurrentUserId();  // ✅ Using your function
$user_name = $is_logged_in ? ($_SESSION['first_name'] ?? 'User') : 'Guest';
$user_email = $is_logged_in ? ($_SESSION['email'] ?? '') : '';

// Create uploads directory if not exists
$upload_dir = 'uploads/cv/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Sample job data
$jobs = [
    [
        'id' => 1,
        'title' => 'Senior Tour Guide',
        'company' => 'ExploreWorld',
        'logo' => 'EW',
        'location' => 'Colombo',
        'type' => 'Full-time',
        'salary' => 'LKR 45,000 - 60,000',
        'experience' => '2-3 years',
        'description' => 'Looking for an experienced tour guide to lead cultural and heritage tours across Sri Lanka.',
        'requirements' => ['Tour Guide License', 'Fluent in English', 'First Aid Certified'],
        'benefits' => ['Health Insurance', 'Performance Bonus', 'Free Tours'],
        'posted_date' => '2 days ago',
        'deadline' => 'Nov 30, 2023',
        'applicants' => 24,
        'rating' => 4.8,
        'featured' => true
    ],
    [
        'id' => 2,
        'title' => 'Tourism Marketing Intern',
        'company' => 'TravelCorp',
        'logo' => 'TC',
        'location' => 'Remote',
        'type' => 'Internship',
        'salary' => 'LKR 25,000 - 30,000',
        'experience' => '0-1 year',
        'description' => 'Join our marketing team to help promote Sri Lankan tourism through digital channels.',
        'requirements' => ['Marketing knowledge', 'Social media skills', 'Content writing'],
        'benefits' => ['Certificate', 'Letter of Recommendation', 'Flexible Hours'],
        'posted_date' => '1 week ago',
        'deadline' => 'Dec 15, 2023',
        'applicants' => 56,
        'rating' => 4.6,
        'featured' => false
    ],
    [
        'id' => 3,
        'title' => 'Hospitality Manager',
        'company' => 'Luxury Resorts',
        'logo' => 'LR',
        'location' => 'Bentota',
        'type' => 'Full-time',
        'salary' => 'LKR 80,000 - 100,000',
        'experience' => '5+ years',
        'description' => 'Manage daily operations of a luxury beach resort. Lead a team of 20+ staff members.',
        'requirements' => ['Hospitality degree', '5+ years experience', 'Leadership skills'],
        'benefits' => ['Accommodation', 'Meals', 'Annual Bonus'],
        'posted_date' => '3 days ago',
        'deadline' => 'Nov 25, 2023',
        'applicants' => 12,
        'rating' => 4.9,
        'featured' => true
    ],
    [
        'id' => 4,
        'title' => 'Travel Consultant',
        'company' => 'Wanderlust Travels',
        'logo' => 'WT',
        'location' => 'Kandy',
        'type' => 'Part-time',
        'salary' => 'LKR 35,000 - 45,000',
        'experience' => '1-2 years',
        'description' => 'Help customers plan their perfect Sri Lankan vacation. Create custom itineraries.',
        'requirements' => ['Travel planning', 'Customer service', 'Sales skills'],
        'benefits' => ['Commission', 'Travel Discounts', 'Flexible Schedule'],
        'posted_date' => '5 days ago',
        'deadline' => 'Dec 10, 2023',
        'applicants' => 34,
        'rating' => 4.7,
        'featured' => false
    ],
    [
        'id' => 5,
        'title' => 'Cultural Heritage Guide',
        'company' => 'Heritage Tours',
        'logo' => 'HT',
        'location' => 'Sigiriya',
        'type' => 'Full-time',
        'salary' => 'LKR 40,000 - 55,000',
        'experience' => '2+ years',
        'description' => 'Specialize in historical and cultural tours in the Cultural Triangle region.',
        'requirements' => ['History background', 'Archaeology knowledge', 'Storytelling skills'],
        'benefits' => ['Accommodation', 'Transport', 'Training'],
        'posted_date' => '1 day ago',
        'deadline' => 'Dec 5, 2023',
        'applicants' => 18,
        'rating' => 4.8,
        'featured' => true
    ],
    [
        'id' => 6,
        'title' => 'Eco-Tourism Specialist',
        'company' => 'Green Lanka',
        'logo' => 'GL',
        'location' => 'Ella',
        'type' => 'Internship',
        'salary' => 'LKR 20,000 - 25,000',
        'experience' => 'Entry level',
        'description' => 'Promote sustainable tourism practices. Work with local communities.',
        'requirements' => ['Environmental awareness', 'Community engagement', 'Research skills'],
        'benefits' => ['Training Certificate', 'Networking', 'Field Trips'],
        'posted_date' => '2 weeks ago',
        'deadline' => 'Dec 20, 2023',
        'applicants' => 42,
        'rating' => 4.5,
        'featured' => false
    ]
];

$job_categories = [
    'All Jobs' => 6,
    'Full-time' => 3,
    'Internship' => 2,
    'Part-time' => 1
];

// ============================================================
// ✅ HANDLE FORM SUBMISSION
// ============================================================
$application_success = false;
$application_error = '';
$applied_job = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    $job_id = (int)$_POST['job_id'];
    $applicant_name = trim($_POST['applicant_name'] ?? '');
    $applicant_email = trim($_POST['applicant_email'] ?? '');
    $applicant_phone = trim($_POST['applicant_phone'] ?? '');
    $cover_letter = trim($_POST['cover_letter'] ?? '');
    $cv_file_path = '';
    
    // --- Validate ---
    if (empty($applicant_name) || empty($applicant_email)) {
        $application_error = 'Please fill in your name and email.';
    } elseif (!filter_var($applicant_email, FILTER_VALIDATE_EMAIL)) {
        $application_error = 'Please enter a valid email address.';
    } else {
        // --- Handle CV upload ---
        if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == 0) {
            $allowed = ['pdf', 'doc', 'docx'];
            $filename = $_FILES['cv_file']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $file_size = $_FILES['cv_file']['size'];
            
            if (!in_array($file_ext, $allowed)) {
                $application_error = 'Only PDF, DOC, or DOCX files are allowed.';
            } elseif ($file_size > 5 * 1024 * 1024) {
                $application_error = 'File size must be less than 5MB.';
            } else {
                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $upload_path)) {
                    $cv_file_path = $upload_path;
                } else {
                    $application_error = 'Failed to upload CV. Please try again.';
                }
            }
        } else {
            $application_error = 'Please upload your CV/Resume (PDF, DOC, or DOCX).';
        }
        
        // --- If no error, save to database ---
        if (empty($application_error)) {
            $job_details = null;
            foreach ($jobs as $job) {
                if ($job['id'] == $job_id) {
                    $job_details = $job;
                    break;
                }
            }
            
            if ($job_details) {
                // ✅ Using your getDBConnection() function
                $conn = getDBConnection();
                $ip_address = $_SERVER['REMOTE_ADDR'];
                
                // Check if table exists
                $table_check = $conn->query("SHOW TABLES LIKE 'job_applications'");
                if ($table_check->num_rows == 0) {
                    $create_sql = "CREATE TABLE job_applications (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        job_id INT NOT NULL,
                        job_title VARCHAR(255) NOT NULL,
                        company VARCHAR(255) NOT NULL,
                        applicant_name VARCHAR(255) NOT NULL,
                        applicant_email VARCHAR(255) NOT NULL,
                        applicant_phone VARCHAR(50),
                        cover_letter TEXT,
                        cv_file_path VARCHAR(500),
                        status ENUM('pending', 'reviewed', 'shortlisted', 'rejected') DEFAULT 'pending',
                        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        ip_address VARCHAR(45),
                        user_id INT
                    )";
                    $conn->query($create_sql);
                }
                
                $sql = "INSERT INTO job_applications (job_id, job_title, company, applicant_name, applicant_email, applicant_phone, cover_letter, cv_file_path, ip_address, user_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $user_id_val = $is_logged_in ? $user_id : null;
                
                $stmt->bind_param("issssssssi", 
                    $job_id, 
                    $job_details['title'], 
                    $job_details['company'], 
                    $applicant_name, 
                    $applicant_email, 
                    $applicant_phone, 
                    $cover_letter, 
                    $cv_file_path, 
                    $ip_address, 
                    $user_id_val
                );
                
                if ($stmt->execute()) {
                    $application_success = true;
                    $applied_job = $job_details;
                } else {
                    $application_error = 'Database error: ' . $stmt->error;
                }
                
                $stmt->close();
                $conn->close();  // ✅ Using your closeConnection function
            } else {
                $application_error = 'Job not found.';
            }
        }
    }
}

// Get filter parameters
$filter_type = isset($_GET['type']) ? $_GET['type'] : 'All Jobs';
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';  // ✅ Using your sanitize function

$filtered_jobs = array_filter($jobs, function($job) use ($filter_type, $search_query) {
    if ($filter_type != 'All Jobs' && $job['type'] != $filter_type) {
        return false;
    }
    if (!empty($search_query)) {
        $search_lower = strtolower($search_query);
        if (strpos(strtolower($job['title']), $search_lower) === false &&
            strpos(strtolower($job['company']), $search_lower) === false &&
            strpos(strtolower($job['location']), $search_lower) === false) {
            return false;
        }
    }
    return true;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Opportunities - SmartTour</title>
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header */
        .header {
            background: white;
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
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 16px;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Search & Filter Bar */
        .search-filter-bar {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-top: -30px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 10;
        }

        .search-form {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-input-group {
            flex: 2;
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .search-input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .filter-select {
            flex: 1;
            padding: 14px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 1rem;
            background: white;
            cursor: pointer;
        }

        .search-btn {
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }

        /* Stats Row */
        .stats-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .results-count {
            font-size: 0.9rem;
            color: var(--gray-600);
        }

        .results-count strong {
            color: var(--primary);
            font-size: 1.2rem;
        }

        /* Job Grid */
        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 24px;
        }

        .job-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
            position: relative;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }

        .featured-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: linear-gradient(135deg, var(--accent), #ff6b9d);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }

        .job-header {
            padding: 20px 20px 0 20px;
            display: flex;
            gap: 16px;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .job-title-section {
            flex: 1;
        }

        .job-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 4px;
        }

        .company-name {
            color: var(--gray-500);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .job-details {
            padding: 16px 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--gray-600);
            background: var(--gray-100);
            padding: 4px 10px;
            border-radius: 20px;
        }

        .detail-item i {
            color: var(--primary);
            font-size: 0.7rem;
        }

        .job-description {
            padding: 16px 20px;
            font-size: 0.85rem;
            color: var(--gray-600);
            line-height: 1.6;
            border-bottom: 1px solid var(--gray-200);
        }

        .job-footer {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .apply-btn {
            background: linear-gradient(135deg, var(--success), var(--success-dark));
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76,201,240,0.3);
        }

        .save-btn {
            background: var(--gray-100);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-500);
        }

        .save-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Quick Apply Modal */
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
            width: 90%;
            max-width: 550px;
            max-height: 85vh;
            overflow-y: auto;
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

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        }

        .modal-header h3 {
            font-size: 1.3rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }

        .modal-body {
            padding: 24px;
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

        .form-group label .required {
            color: var(--danger);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* File Upload */
        .file-upload {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius-md);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--gray-50);
        }

        .file-upload:hover {
            border-color: var(--primary);
            background: rgba(67,97,238,0.05);
        }

        .file-upload i {
            font-size: 2rem;
            color: var(--gray-400);
            margin-bottom: 8px;
        }

        .file-upload p {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .file-upload .file-name {
            margin-top: 8px;
            font-size: 0.8rem;
            color: var(--success);
        }

        #cv_file {
            display: none;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: var(--radius-lg);
            padding: 60px 40px;
            text-align: center;
            animation: fadeInUp 0.5s ease;
            margin: 20px 0;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: white;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }

        .error-message i {
            margin-right: 10px;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: var(--radius-lg);
        }

        .no-results i {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .hero h1 { font-size: 1.8rem; }
            .search-form { flex-direction: column; }
            .jobs-grid { grid-template-columns: 1fr; }
            .job-header { flex-direction: column; align-items: center; text-align: center; }
            .job-details { justify-content: center; }
            .job-footer { flex-direction: column; gap: 12px; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                        <span style="color: var(--gray-600);">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                    <?php else: ?>
                        <a href="login.php" style="text-decoration: none; color: var(--primary);">Login to save jobs</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>🚀 Tourism Job Opportunities</h1>
            <p>Find your dream job in Sri Lanka's tourism industry. Apply instantly with your CV!</p>
        </div>
    </section>

    <div class="container">
        <!-- Search & Filter Bar -->
        <div class="search-filter-bar">
            <form method="GET" action="job-opportunities.php" class="search-form">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search by job title, company, or location..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <select name="type" class="filter-select">
                    <?php foreach($job_categories as $cat => $count): ?>
                        <option value="<?php echo $cat; ?>" <?php echo $filter_type == $cat ? 'selected' : ''; ?>>
                            <?php echo $cat; ?> (<?php echo $count; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="search-btn">Find Jobs</button>
            </form>
        </div>

        <!-- ============================================================
             ✅ DISPLAY SUCCESS MESSAGE
             ============================================================ -->
        <?php if ($application_success && $applied_job): ?>
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Application Submitted! 🎉</h2>
                <p>Your application for <strong><?php echo $applied_job['title']; ?></strong> at <strong><?php echo $applied_job['company']; ?></strong> has been sent successfully.</p>
                <p style="margin-top: 10px;">Your CV has been uploaded and saved with your application.</p>
                <p style="margin-top: 16px;">The employer will review your application and contact you soon.</p>
                <button class="apply-btn" onclick="location.reload()" style="margin-top: 20px;">Browse More Jobs</button>
            </div>

        <!-- ============================================================
             ❌ DISPLAY ERROR MESSAGE
             ============================================================ -->
        <?php elseif ($application_error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($application_error); ?>
            </div>

        <!-- ============================================================
             DISPLAY JOB LISTINGS
             ============================================================ -->
        <?php else: ?>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="results-count">
                <strong><?php echo count($filtered_jobs); ?></strong> jobs found
            </div>
        </div>

        <!-- Jobs Grid -->
        <?php if (empty($filtered_jobs)): ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No jobs found</h3>
                <p>Try adjusting your search or filter criteria</p>
                <button class="apply-btn" onclick="window.location.href='job-opportunities.php'">Clear Filters</button>
            </div>
        <?php else: ?>
            <div class="jobs-grid">
                <?php foreach($filtered_jobs as $job): ?>
                <div class="job-card" data-job-id="<?php echo $job['id']; ?>">
                    <?php if ($job['featured']): ?>
                        <div class="featured-badge">🔥 Featured</div>
                    <?php endif; ?>
                    
                    <div class="job-header">
                        <div class="company-logo"><?php echo $job['logo']; ?></div>
                        <div class="job-title-section">
                            <h3 class="job-title"><?php echo $job['title']; ?></h3>
                            <div class="company-name">
                                <i class="fas fa-building"></i> <?php echo $job['company']; ?>
                                <i class="fas fa-star" style="color: #ffc107;"></i> <?php echo $job['rating']; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="job-details">
                        <span class="detail-item"><i class="fas fa-map-marker-alt"></i> <?php echo $job['location']; ?></span>
                        <span class="detail-item"><i class="fas fa-briefcase"></i> <?php echo $job['type']; ?></span>
                        <span class="detail-item"><i class="fas fa-money-bill"></i> <?php echo $job['salary']; ?></span>
                        <span class="detail-item"><i class="fas fa-clock"></i> Posted <?php echo $job['posted_date']; ?></span>
                    </div>
                    
                    <div class="job-description">
                        <p><?php echo substr($job['description'], 0, 120); ?>...</p>
                    </div>
                    
                    <div class="job-footer">
                        <div>
                            <span class="detail-item" style="background: none;">
                                <i class="fas fa-users"></i> <?php echo $job['applicants']; ?> applicants
                            </span>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="save-btn" onclick="saveJob(<?php echo $job['id']; ?>, '<?php echo addslashes($job['title']); ?>', '<?php echo addslashes($job['company']); ?>')">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="apply-btn" onclick="openApplyModal(<?php echo htmlspecialchars(json_encode($job)); ?>)">
                                <i class="fas fa-paper-plane"></i> Easy Apply
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Quick Apply Modal -->
    <div id="applyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-paper-plane"></i> Quick Apply with CV</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
<form method="POST" action="student-jobs.php" id="applyForm" enctype="multipart/form-data">                <div class="modal-body">
                    <div id="modalJobInfo" style="background: var(--gray-100); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">
                        <!-- Job info will be inserted here -->
                    </div>
                    
                    <input type="hidden" name="job_id" id="job_id">
                    <input type="hidden" name="apply_job" value="1">
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name <span class="required">*</span></label>
                        <input type="text" name="applicant_name" required placeholder="Enter your full name" value="<?php echo $is_logged_in ? htmlspecialchars($user_name) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address <span class="required">*</span></label>
                        <input type="email" name="applicant_email" required placeholder="Enter your email" value="<?php echo $is_logged_in ? htmlspecialchars($user_email) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" name="applicant_phone" placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-file-pdf"></i> Upload CV/Resume <span class="required">*</span></label>
                        <div class="file-upload" onclick="document.getElementById('cv_file').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload your CV (PDF, DOC, or DOCX)</p>
                            <p style="font-size: 0.7rem;">Max file size: 5MB</p>
                            <div class="file-name" id="fileName"></div>
                        </div>
                        <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" style="display: none;" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Cover Letter (Optional)</label>
                        <textarea name="cover_letter" placeholder="Tell us why you'd be great for this role..."></textarea>
                    </div>
                    
                    <div style="background: var(--gray-100); padding: 12px; border-radius: var(--radius-md); font-size: 0.8rem; color: var(--gray-600);">
                        <i class="fas fa-info-circle"></i> By applying, you agree to our terms and privacy policy.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="save-btn" onclick="closeModal()" style="background: var(--gray-200); color: var(--gray-700); padding: 10px 20px; border-radius: var(--radius-md);">Cancel</button>
                    <button type="submit" class="apply-btn" id="submitBtn">Submit Application</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentJob = null;

        function openApplyModal(job) {
            currentJob = job;
            document.getElementById('job_id').value = job.id;
            document.getElementById('modalJobInfo').innerHTML = `
                <strong style="color: var(--primary); font-size: 1rem;">${job.title}</strong><br>
                <span style="font-size: 0.85rem;">${job.company} • ${job.location} • ${job.type}</span><br>
                <span style="font-size: 0.8rem; color: var(--success);">💰 Salary: ${job.salary}</span>
            `;
            document.getElementById('applyModal').style.display = 'flex';
            document.getElementById('fileName').innerHTML = '';
        }

        function closeModal() {
            document.getElementById('applyModal').style.display = 'none';
            document.getElementById('applyForm').reset();
            document.getElementById('fileName').innerHTML = '';
        }

        // File upload handling
        document.getElementById('cv_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileNameSpan = document.getElementById('fileName');
            
            if (file) {
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const fileSize = file.size / 1024 / 1024; // MB
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload a valid PDF, DOC, or DOCX file.');
                    fileNameSpan.innerHTML = '';
                    fileNameSpan.style.color = '#dc3545';
                    fileNameSpan.innerHTML = 'Invalid file type. Please upload PDF, DOC, or DOCX.';
                    setTimeout(() => fileNameSpan.innerHTML = '', 3000);
                    this.value = '';
                    return;
                }
                
                if (fileSize > 5) {
                    alert('File size must be less than 5MB.');
                    fileNameSpan.innerHTML = '';
                    fileNameSpan.style.color = '#dc3545';
                    fileNameSpan.innerHTML = 'File too large. Max 5MB.';
                    setTimeout(() => fileNameSpan.innerHTML = '', 3000);
                    this.value = '';
                    return;
                }
                
                fileNameSpan.style.color = '#28a745';
                fileNameSpan.innerHTML = `✓ ${file.name} (${fileSize.toFixed(2)} MB) - Ready to upload`;
            }
        });

        // ✅ Form validation before submit
        document.getElementById('applyForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('cv_file');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                alert('⚠️ Please upload your CV/Resume (PDF, DOC, or DOCX)');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitBtn.disabled = true;
        });

        function saveJob(jobId, title, company) {
            <?php if ($is_logged_in): ?>
                alert(`"${title}" at ${company} saved to your wishlist!`);
            <?php else: ?>
                if (confirm('Please login to save jobs. Go to login page?')) {
                    window.location.href = 'login.php?redirect=job-opportunities.php';
                }
            <?php endif; ?>
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('applyModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Drag and drop file upload
        const dropZone = document.querySelector('.file-upload');
        if (dropZone) {
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = 'var(--primary)';
                this.style.background = 'rgba(67,97,238,0.05)';
            });
            
            dropZone.addEventListener('dragleave', function(e) {
                this.style.borderColor = 'var(--gray-300)';
                this.style.background = 'var(--gray-50)';
            });
            
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.borderColor = 'var(--gray-300)';
                this.style.background = 'var(--gray-50)';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    document.getElementById('cv_file').files = files;
                    const event = new Event('change');
                    document.getElementById('cv_file').dispatchEvent(event);
                }
            });
        }

        // Auto-close error message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const errorMsg = document.querySelector('.error-message');
            if (errorMsg) {
                setTimeout(function() {
                    errorMsg.style.opacity = '0';
                    setTimeout(function() {
                        errorMsg.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });

        // ✅ If there's a success message, scroll to top
        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = document.querySelector('.success-message');
            if (successMsg) {
                setTimeout(function() {
                    successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    </script>
</body>
</html>