<?php
    require 'db_conn.php'; // Include your database connection

    // Start a session
    session_start();

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, user_name, password, role FROM users WHERE user_name = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Login successful
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['user_id']; // Store user_id in the session
            $_SESSION['username'] = $row['user_name'];
            $_SESSION['role'] = $row['role'];

            if ($password === $row['password']) {
                // Password is correct, redirect to home.php
                header("Refresh: 1; url=home.php");
                echo "<script>alert('Login successful! Redirecting to home...');</script>";
                exit();
            } 
            else {
                // Password is incorrect
                echo "<script>alert('Incorrect password. Please try again.');</script>";
                header("Refresh: 2; url=loginpage.php");
                exit();
            }
        } 
        else {
            // Username not found
            echo "<script>alert('Username not found. Please try again.');</script>";
            header("Refresh: 2; url=loginpage.php");
            exit();
        }
    } 
    else {
        // If not POST, redirect to login page
        header("Location: loginpage.php");
        exit();
    }
?>
