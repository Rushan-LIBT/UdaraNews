<?php
session_start();

// Destroy the session
if (isset($_SESSION['admin_logged_in'])) {
    session_destroy();
}

// Redirect to login page
header('Location: admin_login.php');
exit;
?>