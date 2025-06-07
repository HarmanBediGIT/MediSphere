<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Payment</title>

        <script src="https://www.paypal.com/sdk/js?client-id=ARlGd7ULiv78kwQKZdCqFDMgWo37rwP-bW5mEUZZNe96q5DR-DYIp2zJDnZfJ2O9ky6uBpxqARj9_DHB&currency=USD"></script>


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
        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    </head>

    <body>

        <div class="container">
            <!-- Left Section with Title -->
            <div class="left-section">
                <dotlottie-player
                    src="https://lottie.host/7a36419d-fdb1-4c26-bf2b-f8542f68c815/4NyXIROh7z.lottie"
                    background="transparent"
                    speed="1"
                    style="width: 300px; height: 300px"
                    loop
                    autoplay
                    >
                </dotlottie-player>
                <h2>Order Confirmed ✅</h2>
                <br>
                <p style="color:white;">Payment Successful! We have received your order.</p>
                <p style="color:white;">Your order id : <span id="orderIdText"></span></p>
                <br><br>
                <button class="checkout-btn" onclick="backHome()">Close this window</button>
            </div>

            <!-- Right Section with Payment Form -->
            <!-- <div class="right-section">
                <h2>Choose your Payment method</h2>
                
                <div class="payment-methods">
                    <div class="payment-method" id="paypalTab" onclick="showTab('paypal')">
                        <img src="images/money.png" alt="PayPal">
                        <p>Pay</p>
                    </div>
                </div>
            </div> -->
        </div>
    </body>
</html>

<script>
    function getOrderIdFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('order_id');
    }

    document.addEventListener("DOMContentLoaded", function () {
        const orderId = getOrderIdFromURL();
        if (orderId) {
            document.getElementById("orderIdText").textContent = orderId;
        }
    });
    
    function backHome() {
        window.location.href = "home.php"; // Redirect to home page
    }
</script>