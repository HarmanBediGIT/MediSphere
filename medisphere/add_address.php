<?php
    // Start a session
    session_start();

    require 'db_conn.php';

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // User is not logged in
        $isLoggedIn = false;
    } else {
        // User is logged in
        $isLoggedIn = true;
        // Access the user_id
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $role = $_SESSION['role'];
    }

    $sql = "SELECT * FROM cart";
    $result = $conn->query($sql);

    $sql2 = "SELECT * FROM coupons";
    $result2 = $conn->query($sql2);
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Add Address</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" href="css/address.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
        
        <!-- Bootstrap CSS -->
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    </head>

    <body>
        <!-- Cart Button -->
        <div id="cartButton" class="cart-button">
            <a href="cart.php" style="text-decoration: none; color: inherit;">
                <h2>&#128722; Back to Cart </h2>
            </a>
        </div>

        <!-- Chatbot Button -->
        <!-- <div id="chatbotButton" class="chatbot-button">
            <h2>&#129302;</h2>
        </div> -->

        <!-- Chatbot Interface -->
        <!-- <div id="chatbot" class="chatbot-window" style="display: none;">
            <div class="chatbot-header">
                <h3>XYZ Medical Company Chatbot</h3>
                <span id="closeChatbot" class="close-btn">&times;</span>
            </div>
            <div id="chatWindow" class="chat-window">
                <div id="chatHistory" class="chat-history"> -->
                    <!-- Chat history will appear here -->
                <!-- </div>
                <input type="text" id="userInput" class="user-input" placeholder="Type your message...">
                <button id="sendBtn">Send</button>
            </div>
        </div> -->

        <div class="container">
            <!-- Left Section with Illustration and Title -->
            <div class="left-section">
                <div class="illustration">
                    <!-- Illustration here (medical-themed) -->
                    <img src="https://cdn-icons-png.flaticon.com/512/4317/4317262.png" alt="Medical Illustration">
                </div>
                <h1>Secure Shipping</h1>
                <p>We ensure your package is handled with the utmost care. Please fill in the details to ensure safe and timely delivery.</p>
            </div>

            <!-- Right Section with Form -->
            <div class="right-section">
                <h2>Enter Shipping Address</h2>
                <form action="submit_address.php" method="POST">
                    <div class="form-row">
                        <div class="form-group floating-label">
                            <input type="text" id="firstName" name="first_name" placeholder=" " required>
                            <label for="firstName">First Name</label>
                        </div>
                        <div class="form-group floating-label">
                            <input type="text" id="lastName" name="last_name" placeholder=" " required>
                            <label for="lastName">Last Name</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group floating-label">
                            <input type="email" id="email" name="email" placeholder=" " required>
                            <label for="email">Email</label>
                        </div>
                        <div class="form-group floating-label">
                            <input type="tel" id="phone" name="phone" placeholder=" " required>
                            <label for="phone">Phone Number</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width floating-label">
                            <input type="text" id="address" name="address" placeholder=" " required>
                            <label for="address">Street Address</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group floating-label">
                            <input type="text" id="city" name="city" placeholder=" " required>
                            <label for="city">City</label>
                        </div>
                        <div class="form-group floating-label">
                            <input type="text" id="zip" name="zip" placeholder=" " required>
                            <label for="zip">Postal Code / ZIP</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group floating-label">
                            <label for="country"></label>
                            <select id="country" name="country" required>
                                <option value="" disabled selected>Country</option>
                                <option value="AUS">Australia</option>
                                <option value="IND">India</option>
                                <option value="US">United States</option>
                                <option value="CAD">Canada</option>
                                <option value="UK">United Kingdom</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div>
                        <div class="form-group floating-label">
                            <input type="text" id="state" name="state" placeholder=" " required>
                            <label for="state">State / Province</label>
                        </div>
                    </div>

                    <button type="submit" class="checkout-btn">Confirm Address and Proceed</button>
                </form>
            </div>
        </div>
    </body>
</html>
