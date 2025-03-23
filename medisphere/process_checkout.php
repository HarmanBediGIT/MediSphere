<?php
    session_start();
    require 'db_conn.php';

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: loginpage.php"); // Redirect to login if not logged in
        exit();
    }

    // Access the user_id from the session
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['username'];

    // Get the coupon code and total amount from the POST request
    $appliedCouponCode = $_POST['applied_coupon_code'];
    $totalPrice = $_POST['anotherTotalDisplay']; // Ensure you pass this value from your checkout form

    // Prepare and execute the insert query
    $sql = "UPDATE cart SET price = '$totalPrice' WHERE user_id = '$user_id'";
            
    $stmt = $conn->prepare($sql);

    if ($stmt->execute()) {
        // Successfully inserted
        $sql2 = "UPDATE orders SET total_price = '$totalPrice', coupon_applied = '$appliedCouponCode'
            WHERE user_id = '$user_id'";
            
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute();

        header("Location: add_address.php"); // Redirect to a success page
        exit();
    } else {
        // Handle errors
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
?>
