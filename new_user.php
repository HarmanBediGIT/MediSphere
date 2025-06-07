<?php
    // Start the session to store any flash messages
    session_start();

    // Include your database connection file
    require 'db_conn.php'; // Make sure db_conn.php connects to your MySQL database

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Retrieve user inputs
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phn'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role']; // role selected by the user

        // Check if password and confirm password match
        if ($new_password !== $confirm_password) {
            echo "<script>alert('Passwords do not match. Please try again.'); window.location.href = 'signup.php';</script>";
            exit();
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_name = ?");
        if ($stmt === false) {
            die("Error preparing the statement : " . $conn->error); // Show detailed SQL error
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Username exists
            echo "<script>alert('Username already exists. Please choose a different one.'); window.location.href = 'signup.php';</script>";
            exit();
        }

        // Hash the password for security
        // $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Prepare SQL query to insert the new user
        $role = strtolower($role);
        $stmt = $conn->prepare("INSERT INTO users (name, user_name, email, phn, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $username, $email, $phone, $confirm_password, $role);

        // Execute the query
        if ($stmt->execute()) {
            // User registered successfully
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href = 'loginpage.php';</script>";
        } else {
            // Something went wrong
            echo "<script>alert('Registration failed. Please try again.'); window.location.href = 'signup.php';</script>";
        }

        // Close statement
        $stmt->close();
    }

    // Close database connection
    $conn->close();
?>
