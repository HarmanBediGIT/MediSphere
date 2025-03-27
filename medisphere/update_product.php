<?php
    require 'db_conn.php'; // Replace with your actual database connection file

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the product details from POST request
        $prod_code = $_POST['prod_code'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $colors = $_POST['colors'];
        $sizes = $_POST['sizes'];
        $price = $_POST['price'];
        $qty = $_POST['qty'];

        // Prepare the SQL statement to update the product details
        $sql = "UPDATE products SET name = ?, description = ?, colors = ?, sizes = ?, price = ?, qty = ? WHERE prod_code = ?";
        
        // Prepare the statement
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error preparing the statement: " . $conn->error);  //give complete error on preparing statement failure
        }

        // Bind parameters
        $stmt->bind_param("ssssdis", $name, $description, $colors, $sizes, $price, $qty, $prod_code);

        // Execute the statement
        if ($stmt->execute()) {
            echo "success"; // Send success response
        } 
        else {
            echo "Error updating product: " . $stmt->error; // Send error message
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } 
    else {
        echo "Invalid request method.";   //give this message if form fields are not set by POST method
    }
?>
