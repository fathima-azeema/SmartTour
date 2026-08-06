<?php
// Include database connection
include('db_connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guide_id = 1;  // Example guide ID (replace with session data for logged-in guide)
    $tour_name = $_POST['tour_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $available_dates = $_POST['available_dates'];  // You can store dates in JSON or comma-separated format
    
    // Handle image upload (if any)
    $tour_image = '';
    if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['tour_image']['name']);
        if (move_uploaded_file($_FILES['tour_image']['tmp_name'], $target_file)) {
            $tour_image = $_FILES['tour_image']['name'];
        } else {
            echo "Error uploading image.";
        }
    }
    
    // Insert tour into the database
    $query = "INSERT INTO tours (guide_id, tour_name, description, price, available_dates, image_url) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('issdss', $guide_id, $tour_name, $description, $price, $available_dates, $tour_image);
    $stmt->execute();
    
    echo "Tour profile created successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Tour Profile</title>
    <link rel="stylesheet" href="css/create-profile.css">
</head>
<body>

<!-- Header Section -->
<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="manage-bookings.php">Manage Bookings</a></li>
            <li><a href="promote-tours.php">Promote Tours</a></li>
        </ul>
    </nav>
</header>

<!-- Tour Guide Profile Form -->
<section class="tour-guide-profile">
    <h2>Create Your Tour Guide Profile</h2>
    <form method="POST" action="create-tour-profile.php" enctype="multipart/form-data">
        <label for="tour_name">Tour Name:</label>
        <input type="text" name="tour_name" id="tour_name" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" required>

        <label for="available_dates">Available Dates:</label>
        <input type="text" name="available_dates" id="available_dates" placeholder="Enter available dates (comma-separated)" required>

        <label for="tour_image">Tour Image:</label>
        <input type="file" name="tour_image" id="tour_image" accept="image/*">

        <button type="submit" class="btn">Create Profile</button>
    </form>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 SmartTour. All Rights Reserved.</p>
</footer>

</body>
</html>
