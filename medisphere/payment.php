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

    // Fetch user order details from the database
    $sql = "SELECT * FROM orders WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    $order = $result->fetch_assoc();

    // Fetching address details
    $address = $order['address'];
    $city = $order['city'];
    $zip = $order['postal_code'];
    $phn_num = $order['phn_num'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Payment</title>

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
                <h3>MediSphere Chatbot</h3>
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
            <!-- Left Section with Title -->
            <div class="left-section">
                <img src="https://cdn-icons-png.flaticon.com/512/1126/1126012.png" alt="Payment Illustration" style="width:50%;">
                <br>
                <h1>Secure Payment</h1>
                <p>Please select your payment method and fill in the details to proceed with your order.</p>
            </div>

            <!-- Right Section with Payment Form -->
            <div class="right-section">
                <h2>Choose your Payment method</h2>
                
                <div class="payment-methods">
                    <div class="payment-method" id="creditCardTab" onclick="showTab('stripe')">
                        <img src="images/stripe.png" alt="Stripe">
                        <p>Stripe</p>
                    </div>
                    <div class="payment-method" id="creditCardTab" onclick="showTab('creditCard')">
                        <img src="images/credit.png" alt="Credit Card">
                        <p>Credit Card</p>
                    </div>
                    <div class="payment-method" id="paypalTab" onclick="showTab('paypal')">
                        <img src="images/paypal.png" alt="PayPal">
                        <p>PayPal</p>
                    </div>
                </div>

                <!-- Credit Card Payment Form -->
                <div class="tab-content" id="stripe">
                    <h2>Enter Details for Stripe</h2>
                    <div class="form-group">
                        <input type="text" name="cardHolderName" placeholder="Card Holder's Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="cardNumber" placeholder="Card Number" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="width: 48%; display:inline-block;">
                            <input type="text" name="expDate" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group" style="width: 48%; display:inline-block; margin-left: 2%;">
                            <input type="text" name="cvv" placeholder="CVV" required>
                        </div>
                    </div>
                    <button type="button" class="checkout-btn">Pay Now</button>
                </div>

                <!-- Credit Card Payment Form -->
                <div class="tab-content" id="creditCard">
                    <h2>Enter Credit Card Details</h2>
                    <div class="form-group">
                        <input type="text" name="cardHolderName" placeholder="Card Holder's Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="cardNumber" placeholder="Card Number" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="width: 48%; display:inline-block;">
                            <input type="text" name="expDate" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group" style="width: 48%; display:inline-block; margin-left: 2%;">
                            <input type="text" name="cvv" placeholder="CVV" required>
                        </div>
                    </div>
                    <button type="button" class="checkout-btn">Pay Now</button>
                </div>

                <!-- PayPal Payment Form -->
                <div class="tab-content" id="paypal">
                    <h2>Log in to PayPal</h2>
                    <div class="form-group">
                        <input type="email" name="paypalEmail" placeholder="PayPal Email" required>
                    </div>
                    <button type="button" class="checkout-btn">Proceed to PayPal</button>
                </div>

                <!-- Billing Address -->
                <div class="billing-info">
                    <h2>Billing Address</h2>
                    <div class="form-group">
                        <input type="text" name="billingAddress" id="billingAddress" placeholder="Street Address" value="<?php echo htmlspecialchars($address); ?>" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="width: 48%; display:inline-block;">
                            <input type="text" name="billingCity" id="billingCity" placeholder="City" value="<?php echo htmlspecialchars($city); ?>" readonly>
                        </div>
                        <div class="form-group" style="width: 48%; display:inline-block; margin-left: 4%;">
                            <input type="text" name="billingZip" id="billingZip" placeholder="Postal Code" value="<?php echo htmlspecialchars($zip); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="billingPhone" id="billingPhone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phn_num); ?>" readonly>
                    </div>
                    <a href="add_address.php" style="text-decoration:none;">
                        <button type="button" class="checkout-btn">Change Address</button>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
        }
</script>