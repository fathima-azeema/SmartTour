<?php

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

function getHotelImage($location) {
    $images = [
        'sigiriya' => 'https://i.pinimg.com/736x/0e/ed/2a/0eed2a5464d29acbc937f032b3c4a2ee.jpg',
        'colombo' => 'https://islandwyde.com/wp-content/uploads/2025/08/a90aa71efd.jpg',
        'kandy' => 'https://media-cdn.tripadvisor.com/media/photo-s/19/0b/ff/73/picturesquely-located.jpg',
        'mirissa' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800',
        'nuwaraeliya' => 'https://i0.wp.com/thelandofwanderlust.com/wp-content/uploads/2025/07/514659794_832586715960069_6484689975201655604_n.jpg'
    ];
    
    return $images[$location] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800';
}
?>