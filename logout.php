<?php
require_once 'config.php';


$_SESSION['logout_message'] = "You have been successfully logged out.";

session_unset();
session_destroy();

setcookie(session_name(), '', time() - 3600, '/');

redirect('login.php');
?>