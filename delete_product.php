<?php
// Include your database connection file
require 'db_conn.php'; // Replace with your actual database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product code from the POST request
    $code = $_POST['code'];

    // Prepare the SQL statement to delete the product from the database
    $sql = "DELETE FROM products WHERE prod_code = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("s", $code);

    // Execute the statement
    if ($stmt->execute()) {
        echo 'success'; // Return success message
    } else {
        echo 'Error: ' . $stmt->error; // Return error message
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
