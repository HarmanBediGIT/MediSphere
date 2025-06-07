<?php
    // Start a session
    session_start();
    require "db_conn.php";

    if (!isset($_SESSION['user_id'])) {
        // User is not logged in
        $isLoggedIn = false;
    } 
    else {
        // User is logged in
        $isLoggedIn = true;
        // Access the user_id
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $role = $_SESSION['role'];
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capture form data
        $first_name = $conn->real_escape_string($_POST['first_name']);
        $last_name = $conn->real_escape_string($_POST['last_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $address = $conn->real_escape_string($_POST['address']);
        $city = $conn->real_escape_string($_POST['city']);
        $state = $conn->real_escape_string($_POST['state']);
        $zip = $conn->real_escape_string($_POST['zip']);
        $country = $conn->real_escape_string($_POST['country']);

        // Capture product info
        $product_id = $_POST['product_id'];
        $product_name = urlencode($_POST['name']);
        $product_price = $_POST['price'];

        // Prepare the SQL statement
        $sql = "UPDATE orders SET name = CONCAT('$first_name', ' ', '$last_name'),
                phn_num = '$phone',
                address = '$address',
                city = '$city',
                state = '$state',
                postal_code = '$zip',
                country = '$country'
            WHERE user_id = '$user_id'";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            // Redirect to a thank you page or display success message
            echo "<script>
                    // alert('Address has been successfully submitted!');
                    window.location.href = 'payment.php?product_id=$product_id&name=$product_name&price=$product_price'; // Redirect after successful insert
                </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the connection
        $conn->close();
    }
?>