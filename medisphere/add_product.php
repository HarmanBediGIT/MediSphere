<?php
    error_reporting(0);
    ini_set('display_errors', '0');

    session_start();
    require 'db_conn.php';

    // Check if the user is an admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit();
    }

    // Get the JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    $code = $conn->real_escape_string($data['code']);
    $name = $conn->real_escape_string($data['name']);
    $description = $conn->real_escape_string($data['description']);
    $qty = $conn->real_escape_string($data['qty']);
    $price = $conn->real_escape_string($data['price']);

    // Insert into the database
    $sql = "INSERT INTO products (code, name, description, qty, price) VALUES ('$code', '$name', '$description', '$qty', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo 'Product added successfully!'; // Success message
    } 
    else {
        echo 'Error : Product cannot be added !'; // Error message
    }
    $conn->close();
?>
