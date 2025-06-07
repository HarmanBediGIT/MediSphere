<?php
    // Include your database connection file
    require 'db_conn.php'; // Replace with your actual database connection file

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the product code, user id, rating, and review text from the form
        $product_code = $_POST['product_code'];
        $user_id = $_POST['user_id'];
        $rating = $_POST['rating'];
        $review_text = $_POST['review_text'];

        // Prepare the SQL statement to check if a review already exists
        $checkSql = "SELECT * FROM reviews WHERE product_code = ? AND user_id = ?";
        
        // Prepare the check statement
        $checkStmt = $conn->prepare($checkSql);
        if ($checkStmt === false) {
            die("Error preparing the statement: " . $conn->error);
        }

        // Bind parameters
        $checkStmt->bind_param("si", $product_code, $user_id);

        // Execute the check statement
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        // Check if the user has already submitted a review for the product
        if ($result->num_rows > 0) {
            // User has already reviewed the product, so update the existing review
            $updateSql = "UPDATE reviews SET rating = ?, review_text = ? WHERE product_code = ? AND user_id = ?";
            
            // Prepare the update statement
            $updateStmt = $conn->prepare($updateSql);
            if ($updateStmt === false) {
                die("Error preparing the update statement: " . $conn->error);
            }

            // Bind parameters for the update
            $updateStmt->bind_param("issi", $rating, $review_text, $product_code, $user_id);

            // Execute the update statement
            if ($updateStmt->execute()) {
                echo "Review updated successfully. You will be redirected shortly.";
                header("refresh:1;url=detailed_products.php?id=" . urlencode($product_code));
                exit; // Exit to ensure no further code is executed
            } 
            else {
                echo "Error updating review: " . $updateStmt->error;
            }

            // Close the update statement
            $updateStmt->close();
        } 
        else {
            // User has not reviewed the product yet, so insert a new review
            $insertSql = "INSERT INTO reviews (product_code, user_id, rating, review_text) VALUES (?, ?, ?, ?)";
            
            // Prepare the insert statement
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt === false) {
                die("Error preparing the insert statement: " . $conn->error);
            }

            // Bind parameters for the insert
            $insertStmt->bind_param("siis", $product_code, $user_id, $rating, $review_text);

            // Execute the insert statement
            if ($insertStmt->execute()) {
                echo "Review submitted successfully. You will be redirected shortly.";
                header("refresh:1;url=detailed_products.php?id=" . urlencode($product_code));
                exit; // Exit to ensure no further code is executed
            } 
            else {
                echo "Error submitting review: " . $insertStmt->error;
            }

            // Close the insert statement
            $insertStmt->close();
        }

        // Close the check statement and connection
        $checkStmt->close();
        $conn->close();
    } 
    else {
        echo "Invalid request method.";
    }
?>
