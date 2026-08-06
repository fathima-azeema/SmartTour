<?php
// apply.php
include('db_connection.php');

// Get job_id from the URL (the job the student is applying for)
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : 0;

// Fetch job details from the database to display on the page
$query = "SELECT * FROM jobs WHERE job_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    echo "Job not found!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $cover_letter = $_POST['cover_letter'];

    // Handle file upload (resume)
    $resume = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $target_dir = "uploads/"; // Make sure this folder exists
        $target_file = $target_dir . basename($_FILES['resume']['name']);  // File path

        // Check if the file was uploaded successfully
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
            $resume = $_FILES['resume']['name']; // Store the filename
        } else {
            echo "Error uploading file.";
            exit;
        }
    }

    // Insert the application into the database
    $query = "INSERT INTO applications (job_id, full_name, email, phone, resume, cover_letter) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssss', $job_id, $full_name, $email, $phone, $resume, $cover_letter);
    if ($stmt->execute()) {
        echo "Application submitted successfully!";
    } else {
        echo "Error submitting application.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job - <?php echo $job['job_title']; ?></title>
    <link rel="stylesheet" href="css/apply.css">
<link rel="stylesheet" href="css/header.css">
</head>
<body>

<!-- Header Section -->
<header id="header" class="header sticky">
    <div class="container">
        <div class="header-content">
            <!-- Logo Section -->
            <a href="index.html" class="logo">
                <i data-lucide="map-pin" class="logo-icon"></i>
                <span class="logo-text"> ✈︎⁀𓍙SmartTour</span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="nav-desktop">
                <a href="index.html" class="nav-link active">Home</a>
                <a href="about-us.php" class="nav-link">About</a>
                <a href="services.html" class="nav-link">Services</a>
                <a href="contact.html" class="nav-link">Contact</a>
            </nav>

            <!-- User Actions (Login and Sign Up) -->
            <div class="header-actions">
                <button class="btn btn-secondary">Login</button>
                <button class="btn btn-primary" onclick="window.location.href='signup.html'">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<!-- Job Application Section -->
<section class="job-application">
    <h2>Apply for <?php echo $job['job_title']; ?> at <?php echo $job['company_name']; ?></h2>

    <!-- Application Form -->
    <form action="apply.php?job_id=<?php echo $job['job_id']; ?>" method="POST" enctype="multipart/form-data">
        <!-- Personal Details -->
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" required>

        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" id="phone">

        <!-- Resume Upload -->
        <label for="resume">Upload Resume (PDF, DOC, DOCX):</label>
        <input type="file" name="resume" id="resume" accept=".pdf, .doc, .docx" required>

        <!-- Cover Letter -->
        <label for="cover_letter">Cover Letter:</label>
        <textarea name="cover_letter" id="cover_letter" rows="5" required></textarea>

        <!-- Submit Button -->
        <button type="submit" class="btn">Submit Application</button>
    </form>
</section>

<!-- Footer Section -->
    <!-- Footer -->
<?php include 'footer.php'; ?>
</body>
</html>
