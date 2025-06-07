
<?php
session_start();
require 'db_conn.php';

// Detect if it's a Buy Now payment
$isBuyNow = isset($_GET['product_id'], $_GET['name'], $_GET['price']);
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
if ($isBuyNow) {
    $product_id = $_GET['product_id'];
    $product_name = $_GET['name'];
    $product_price = $_GET['price'];
} else {
    // Default values for cart-based flow
    if (!isset($_SESSION['user_id'])) {
        $isLoggedIn = false;
    } else {
        $isLoggedIn = true;
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $role = $_SESSION['role'];
    }

    // $sql = "SELECT * FROM orders WHERE user_id = '$user_id'";
    // $result = $conn->query($sql);
    // $order = $result->fetch_assoc();

    // $address = $order['address'];
    // $city = $order['city'];
    // $zip = $order['postal_code'];
    // $phn_num = $order['phn_num'];
    // $paypalAmount = isset($_SESSION['final_total']) ? $_SESSION['final_total'] : 0;
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

    $paypalAmount = isset($_SESSION['final_total']) ? $_SESSION['final_total'] : 0;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <script src="https://www.paypal.com/sdk/js?client-id=ARlGd7ULiv78kwQKZdCqFDMgWo37rwP-bW5mEUZZNe96q5DR-DYIp2zJDnZfJ2O9ky6uBpxqARj9_DHB&currency=USD"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
    <link rel="stylesheet" href="css/address.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
</head>

<body>
    <div id="cartButton" class="cart-button" onclick="openCart()">
        <i class="fas fa-shopping-cart"> </i>
    </div>

    <div class="container">
        <div class="left-section">
            <img src="https://cdn-icons-png.flaticon.com/512/1126/1126012.png" alt="Payment Illustration" style="width:50%;">
            <br>
            <h1>Secure Payment</h1>
            <p>Please select your payment method and fill in the details to proceed with your order.</p>
        </div>

        <div class="right-section">
            <h2>Choose your Payment method</h2>

            <div class="tab-content active" id="paypal">
                <div id="paypal-button-container"></div>
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

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
}

function openCart() {
    window.location.href = "cart.php";
}

<?php if ($isBuyNow): ?>
const productId = <?= json_encode($product_id); ?>;
const productName = <?= json_encode($product_name); ?>;
const productPrice = <?= json_encode($product_price); ?>;
<?php else: ?>
const finalAmount = <?= json_encode(number_format($paypalAmount, 2, '.', '')); ?>;
<?php endif; ?>

paypal.Buttons({
    style: {
        layout: 'vertical',
        color: 'blue',
        shape: 'rect',
        label: 'paypal',
        tagline: false
    },
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                description: <?= $isBuyNow ? "productName" : "'Cart Checkout'" ?>,
                amount: {
                    value: <?= $isBuyNow ? "productPrice" : "finalAmount" ?>
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            fetch('payment_success.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    orderID: data.orderID,
                    payerID: data.payerID,
                    amount: details.purchase_units[0].amount.value,
                    payerEmail: details.payer.email_address,
                    payerName: details.payer.name.given_name + ' ' + details.payer.name.surname,
                    <?php if ($isBuyNow): ?>
                    productId: productId,
                    singleBuy: true
                    <?php endif; ?>
                })
            }).then(res => {
                if (res.ok) {
                    window.location.href = 'thank_you.php?order_id=' + data.orderID;
                } else {
                    alert('Failed to record transaction.');
                }
            });
        });
    },
    onError: function(err) {
        console.error('PayPal Checkout onError', err);
        alert('There was an error with PayPal Checkout.');
    }
}).render('#paypal-button-container');
</script>

</html>
