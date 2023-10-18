<?php
session_start();

if (isset($_POST['logout'])) {
    // Unset all session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to the login page or any other desired page
    header('Location: index.php');
    exit;
} else {
    // Redirect to the dashboard if the logout button was not clicked
    header('Location: dashboard.php');
    exit;
}
