<?php
    // Database connection
    include 'db_conn.php';  // Include your database connection file

    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];  // Phone number now includes country code
    $message = $_POST['message'];

    // Insert data into the database
    $sql = "INSERT INTO contact (name, email, phn, msg) VALUES ('$name', '$email', '$phone', '$message')";
    if (mysqli_query($conn, $sql)) {
        echo "Message sent successfully!";
        header("refresh:2; url=contact.php");  // Redirect back after 2 seconds
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
?>
