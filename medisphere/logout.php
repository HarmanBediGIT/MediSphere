<?php
    session_start(); // Start the session

    // Destroy all session data
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session

    // clear cache
    header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
    header("Pragma: no-cache"); // HTTP 1.0
    header("Expires: 0"); // Proxies

    // Redirect to login page
    header("Location: mainpage.php");
    exit();
?>
