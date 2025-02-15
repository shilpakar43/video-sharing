<?php 
    include("./include/conn.php");
    include("./include/session.php");
    session_start(); // Start the session

    // Destroy all session variables and the session itself
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session

    // Clear session cookies (if any)
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/'); // Expire the session cookie
    }

    // Clear any custom cookies (like 'username')
    setcookie('username', '', time() - 3600, '/'); // Expire the 'username' cookie

    // Redirect to login page
    header('Location: login.php');
    exit(); // Stop script execution after redirection
?>
