<?php
    // Database connection (replace with your actual connection details)
    $host = 'localhost';
    $db = 'xyz_medical_company';
    $user = 'root';
    $pass = '';

    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['search'])) {
        $search = $_POST['search'];

        // Prepare a SQL statement to search for products
        $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :search LIMIT 10");
        $stmt->execute(['search' => "%$search%"]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debugging: Check if results are returned
        error_log(print_r($products, true));  // Log result to server

        // Return the results as JSON
        echo json_encode($products);
    }
?>
