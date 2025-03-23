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
    $priceRange = $conn->real_escape_string($data['priceRange']);

    // Insert into the database
    $sql = "INSERT INTO categories (code, name, price_range) VALUES ('$code', '$name', '$priceRange')";

    if ($conn->query($sql) === TRUE) {
        echo 'Category added successfully!'; // Success message
    } 
    else {
        echo 'Error : Category cannot be added !'; // Error message
    }
    $conn->close();
?>
