<?php
// admin-page.php - Accessible only to admins and guides
require_once 'config.php';
require_once 'session-check.php';

// Allow only admins and guides
requireUserTypes(['admin', 'guide']);

// Rest of your code...
?>