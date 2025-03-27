<?php
    // Start a session
    session_start();

    require 'db_conn.php';

    // Check if the user is logged in
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
    }// Initialize an array to hold the remaining items after deletion
    $remainingItemsArray = [];

    // Check if an item removal has been submitted
    if (isset($_POST['remove_item'])) {
        $itemToRemove = $_POST['remove_item'];
    
        // Fetch the current items in the cart for the user
        $sql = "SELECT * FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $items = $row['items']; // Comma-separated string of items
            
            // Convert the items to an array
            $itemsArray = explode(',', $items);
    
            // Filter out the removed item from the array
            $updatedItemsArray = array_filter($itemsArray, function($item) use ($itemToRemove) {
                return trim($item) !== $itemToRemove;
            });
    
            // Convert the updated array back to a comma-separated string
            $updatedItems = implode(',', $updatedItemsArray);
    
            // Update the database with the new items list in the cart
            $updateSql = "UPDATE cart SET items = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $updatedItems, $user_id);
    
            if ($updateStmt->execute()) {
                // Also remove the item from the orders table
                // Fetch current orders for the user
                $orderSql = "SELECT * FROM orders WHERE user_id = ?";
                $orderStmt = $conn->prepare($orderSql);
                $orderStmt->bind_param("i", $user_id);
                $orderStmt->execute();
                $orderResult = $orderStmt->get_result();
    
                if ($orderResult->num_rows > 0) {
                    while ($orderRow = $orderResult->fetch_assoc()) {
                        $orderItems = $orderRow['items']; // Comma-separated string of items
                        $orderItemsArray = explode(',', $orderItems);
    
                        // Filter out the removed item from the order items
                        $updatedOrderItemsArray = array_filter($orderItemsArray, function($item) use ($itemToRemove) {
                            return trim($item) !== $itemToRemove;
                        });
    
                        // Convert the updated order items array back to a comma-separated string
                        $updatedOrderItems = implode(',', $updatedOrderItemsArray);
    
                        // Update the database with the new items list in orders
                        $orderUpdateSql = "UPDATE orders SET items = ? WHERE user_id = ? AND items = ?";
                        $orderUpdateStmt = $conn->prepare($orderUpdateSql);
                        $orderUpdateStmt->bind_param("sis", $updatedOrderItems, $user_id, $orderItems);
    
                        // Execute the order update
                        $orderUpdateStmt->execute();
    
                        // Close the order update statement
                        $orderUpdateStmt->close();
                    }
                }
    
                // Set a session variable to track that the item was removed
                $_SESSION['item_removed'] = true;
                // Redirect to the same page to avoid re-executing the removal logic on refresh
                header("Location: cart.php");
                exit(); // Stop further execution
            } 
            else {
                echo "Error removing item: " . $conn->error;
            }
    
            // Close the statement after updating the cart
            $updateStmt->close();
        }
    
        // Close the select statement for cart
        $stmt->close();
    }
    
    // Show the alert if the item was removed successfully
    if (isset($_SESSION['item_removed']) && $_SESSION['item_removed'] === true) {
        echo "<script> alert('Item removed successfully!'); </script>";
        // Unset the session variable to prevent showing the alert again on refresh
        unset($_SESSION['item_removed']);
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Cart</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>

        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    </head>

    <body>
        <div id="progressBarContainer">
            <div id="progressBar"></div>
        </div>
        
        <nav>
            <div class="logo">
                    <img src="images/medisphere.png" style="width:110px;"> </img>
                    <h5 style="font-size:25px; margin-left:10px;"> MediSphere </h5>
                    <?php if ($isLoggedIn): ?>
                        <!-- <br>  -->
                        <h5 style="font-size:15px;"> <br> <br> Welcome <?php echo $name; ?> </h5>
                    <?php endif; ?>
            </div>

            <input type="checkbox" id="click">
            <label for="click" class="menu-btn">
                <i class="fas fa-bars"></i>
            </label>

            <ul>
                <li><a href="index.php">index</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <?php
                        // Check if the logged-in user is an admin
                        if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                    ?>
                        <li><a href="orders.php">Order Status</a></li>
                    <?php } ?>
                <?php else: ?>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="loginpage.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Chatbot Button -->
        <!-- <div id="chatbotButton" class="chatbot-button">
            <h2>&#129302;</h2>
        </div> -->

        <!-- Chatbot Interface -->
        <!-- <div id="chatbot" class="chatbot-window" style="display: none;">
            <div class="chatbot-header">
                <h3>MediSphere Chatbot</h3>
                <span id="closeChatbot" class="close-btn" style="color:white;">&times;</span>
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
            <div class="profile-container">
                <h1>My Cart</h1>
            </div>

            <?php 
                // Fetch the updated cart after the removal process
                $sql = "SELECT * FROM cart WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Fetch the row with user's cart items
                    $row = mysqli_fetch_assoc($result);
                    $items = explode(',', $row['items']); // Split items into an array
                    $itemsString = implode(',', $items);
                    $sqlQuery = "INSERT INTO orders (user_id, user_name, items) VALUES (?, ?, ?)";
                    $stmt2 = $conn->prepare($sqlQuery);
                    if(!$stmt2){
                        die("Statement preparation failed: " . mysqli_error($conn));
                    }
                    $stmt2->bind_param("iss", $user_id, $name, $itemsString);
                    $stmt2->execute();

                    // Check if there are still items in the cart
                    if (!empty($items) && count(array_filter($items)) > 0) {
                        // There are items in the cart
                        foreach ($items as $item) {
                            $item = trim($item);

                            // Fetch price for each item from 'products' table
                            $price_sql = "SELECT price FROM products WHERE name = ?";
                            $stmt = $conn->prepare($price_sql);
                            $stmt->bind_param("s", $item);
                            $stmt->execute();
                            $price_result = $stmt->get_result();

                            if ($price_row = $price_result->fetch_assoc()) {
                                $price = $price_row['price'];
                            } 
                            else {
                                $price = 0; // Default to 0 if no price found
                            }
                            ?>

                            <!-- Cart Item Display with Quantity Selector -->
                            <div class="profile-container" data-item="<?php echo htmlspecialchars($item); ?>" style="display: flex; align-items: center; justify-content: space-between; background-color: #f0f8f8; padding: 15px; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                                <!-- Product Image -->
                                <div style="flex: 0 0 auto; margin-right: 15px;">
                                    <img src="" alt="<?php echo htmlspecialchars($item); ?>" style="width: 80px; height: auto; border-radius: 5px;">
                                </div>
                                
                                <div class="profile-details">
                                    <h3>Item Name: <?php echo htmlspecialchars($item); ?></h3>
                                    <h4>Price: <span id="price-<?php echo htmlspecialchars($item); ?>" data-base-price="<?php echo $price; ?>">$ <?php echo number_format((float)$price, 2, '.', ''); ?></span></h4>
                                </div>

                                <!-- Quantity Selector -->
                                <div class="quantity-box" style="display: flex; align-items: center;">
                                    <button class="btn-decrease" data-item="<?php echo htmlspecialchars($item); ?>" style="font-size: 20px; background-color: #e0f2f2; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">-</button>
                                    <div id="quantity-<?php echo htmlspecialchars($item); ?>" class="item-quantity" style="margin: 0 10px; font-size: 18px;">1</div>
                                    <button class="btn-increase" data-item="<?php echo htmlspecialchars($item); ?>" style="font-size: 20px; background-color: #e0f2f2; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">+</button>
                                </div>
                            </div>

                            <?php 
                        }
                    } 
                    else {
                        // No items in the cart, delete the entry from the cart table
                        $delete_sql = "DELETE FROM cart WHERE user_id = ?";
                        $delete_stmt = $conn->prepare($delete_sql);
                        $delete_stmt->bind_param("i", $user_id);
                        $delete_stmt->execute();

                        // No items in the cart, delete the entry from the orders table
                        $delete_sql2 = "DELETE FROM orders WHERE user_id = ?";
                        $delete_stmt2 = $conn->prepare($delete_sql2);
                        $delete_stmt2->bind_param("i", $user_id);
                        $delete_stmt2->execute();

                        ?>
                        <div class="empty-cart-container">
                            <img src="images/cart.png" alt="Empty Cart" class="empty-cart-img">
                            <h2 class="empty-cart-text">Your Cart is Empty</h2>
                            <p class="empty-cart-description">It looks like you haven't added any items to your cart yet.</p>
                        </div>
                        <?php
                        $totalAmount = 0; // No total amount if cart is empty
                    }
                } 
                else {
                    ?>
                    <div class="empty-cart-container">
                        <img src="images/cart.png" alt="Empty Cart" class="empty-cart-img">
                        <h2 class="empty-cart-text">Your Cart is Empty</h2>
                        <p class="empty-cart-description">It looks like you haven't added any items to your cart yet.</p>
                    </div>
                    <?php
                    $totalAmount = 0; // No total amount if cart is empty
                }

                // Set up default values for taxes and delivery charges
                $taxRate = 0.10; // 10% tax
                $deliveryCharges = 5.00; // Example fixed delivery charges
                $finalAmount = 0; // Initialize final amount
            ?>

            <div class="checkout-container">
                <h2>Checkout</h2>
                <div class="checkout-details">
                    <div class="checkout-item">
                        <label>Item Total : </label>
                        <span id="itemTotal">$0.00</span> <!-- Dynamic item total -->
                    </div>
                    <div class="checkout-item">
                        <label>Taxes (10%) : </label>
                        <span id="taxAmount">$0.00</span> <!-- Dynamic tax total -->
                    </div>
                    <div class="checkout-item">
                        <label class="popover-button-default" data-content=""  title="Delivery Charges Breakdown" data-trigger="hover" data-placement="left">
                            Delivery Charges : <i class="glyph-icon icon-info-circle" style="float: right; font-size: 15px; text-align: center;"></i>
                        </label>
                        <span id="deliveryChargesAmount">$<?php echo number_format($deliveryCharges, 2); ?></span>
                    </div>
                    <div class="checkout-item"> 
                        <label>Coupon Code : </label>
                        <input type="text" id="couponCode" name="coupon_code" placeholder="Enter coupon code">
                        <button id="showCoupon" class="apply-btn" data-toggle="modal" data-target="#couponModal">Show Coupons</button> 
                        <button id="applyCoupon" class="apply-btn">Apply</button>
                    </div>
                    <div class="checkout-item">
                        <label>Discount : </label>
                        <span id="discountAmount" style="color:#4caf50;">-$<?php echo number_format(0, 2); ?></span> 
                    </div>
                    <div class="checkout-item total-amount">
                        <label>Final Total : </label>
                        <span id="finalTotal">$<?php echo number_format($finalAmount, 2); ?></span> <!-- Final total will be calculated later -->
                    </div>
                    <div class="checkout-action">
                        <form action="process_checkout.php" method="POST" id="checkoutForm">
                            <input type="hidden" id="anotherTotalDisplay" name="anotherTotalDisplay" readonly>
                            <button type="submit" id="checkoutButton" class="checkout-btn" onclick="setFinalTotal()">Proceed Checkout</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="couponModalLabel">Available Coupons</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <!-- <span aria-hidden="true">&times;</span> -->
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul id="couponList" class="list-group">
                                <?php 
                                    // Fetch coupons that are less than the total amount
                                    $sqlCoupons = "SELECT coupon_code, amount FROM coupons WHERE amount < ?";
                                    $stmtCoupons = $conn->prepare($sqlCoupons);
                                    $stmtCoupons->bind_param("d", $finalAmount); // Bind the total amount
                                    $stmtCoupons->execute();
                                    $resultCoupons = $stmtCoupons->get_result();

                                    // Check if any coupons were found
                                    if ($resultCoupons->num_rows > 0) {
                                        // Loop through the coupons and display them
                                        while ($coupon = $resultCoupons->fetch_assoc()) {
                                            echo '<li class="list-group-item coupon-item" data-coupon-code="' . htmlspecialchars($coupon['coupon_code']) . '" data-coupon-amount="' . htmlspecialchars($coupon['amount']) . '">'
                                                . htmlspecialchars($coupon['coupon_code']) . '  -  $' . number_format($coupon['amount'], 2) . '</li>';
                                        }
                                    } 
                                    else {
                                        echo '<li class="list-group-item">No coupons available.</li>'; // Message if no coupons found
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hidden form to submit item removal -->
        <form id="removeItemForm" action="cart.php" method="POST" style="display: none;">
            <input type="hidden" name="remove_item" id="removeItemInput">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        </form>

        <div class="footer1">
            <div class="social">
                <a href="#">
                    <i class="fa-brands fa-square-instagram"></i>
                </a>
                <a target="_blank" href="#">
                    <i class="fa-brands fa-facebook"></i>
                </a>
            </div>
            <div class="copyright">
                © Copyright 2023, All rights reserved
            </div>
        </div>

    </body>
</html>

<script>
    $(document).ready(function() {
        // Event listener for coupon selection
        $(document).on('click', '.coupon-item', function() {
            var couponCode = $(this).data('coupon-code');
            var couponAmount = parseFloat($(this).data('coupon-amount'));
            $('#couponCode').val(couponCode); // Set the coupon code in the input field
            
            // Calculate and display the discount
            var totalAmount = parseFloat($('#finalTotal').text().replace('$', ''));
            var finalTotal = totalAmount - couponAmount; // Recalculate final total
            $('#discountAmount').text('-$' + couponAmount.toFixed(2)); // Update discount display
            $('#finalTotal').text('$' + finalTotal.toFixed(2)); // Update final total
            $('#couponModal').modal('hide'); // Close the modal
        });

        // Event listener for applying coupon
        $('#applyCoupon').click(function() {
            var couponCode = $('#couponCode').val();
            alert('Coupon ' + couponCode + ' applied!'); // Placeholder action
        });
    });

    function setCouponCode() {
        var couponCode = $('#couponCode').val();
        $('#appliedCouponCode').val(couponCode); // Set the coupon code in the hidden input
        var finalTotal = $('#finalTotal').text().replace('$', ''); // Get the final total value
        $('#finalTotalAmount').val(finalTotal); // Set the final total in the hidden input
    }
    
    // Function to update the quantity and price
    function updateQuantity(item, action) {
        console.log("Updating quantity for item: ", item);
        let quantityElement = document.getElementById('quantity-' + item);
        console.log("Quantity Element: ", quantityElement);

        let priceElement = document.getElementById('price-' + item);
        let basePrice = parseFloat(priceElement.getAttribute('data-base-price')); // Base price of the item
        let currentQuantity = parseInt(quantityElement.innerText);

        if (action === 'increase') {
            currentQuantity += 1;
        } 
        else if (action === 'decrease') {
            if (currentQuantity > 1) {
                currentQuantity -= 1;
            } 
            else {
                let confirmRemove = confirm('Are you sure you want to remove this item?');
                if (confirmRemove) {
                    removeItem(item);
                    return; // Exit function after removing the item
                }
            }
        }

        // Update the quantity displayed
        quantityElement.innerText = currentQuantity;

        // Update the price displayed based on the quantity
        let newPrice = (basePrice * currentQuantity).toFixed(2);
        priceElement.innerText = '$ ' + newPrice;

        // Save the updated quantity in localStorage
        saveQuantityToLocalStorage(item, currentQuantity);

        // Update the checkout total after changing quantity
        updateCheckoutTotal();
    }

    // Function to visually remove the item from the cart
    function removeItem(item, quantity) {
        let itemContainer = document.querySelector('.profile-container[data-item="' + item + '"]');
        if (itemContainer) {
            itemContainer.remove();
        }
        // Set the value of the hidden form input to the item name
        document.getElementById('removeItemInput').value = item;
        
        // Submit the form
        document.getElementById('removeItemForm').submit();

        // Remove the item from localStorage
        localStorage.removeItem('cartItem-' + item, quantity);

        // Update the checkout total after removal
        updateCheckoutTotal();
    }

    // Function to update the item total in the checkout container
    function updateCheckoutTotal() {
        let itemTotal = 0; // Initialize item total
        let taxAmount = 0;
        let discountAmount = 0;
        let finalTotal = 0;
        let deliveryCharges = parseFloat(document.getElementById('deliveryChargesAmount').innerText.replace('$', '')); // Get delivery charges from the span


        // Loop through all items in the cart
        document.querySelectorAll('.profile-container').forEach(container => {
            const itemName = container.getAttribute('data-item');
            
            console.log("Processing item: ", itemName);

            const quantityElement = document.getElementById('quantity-' + itemName);
            const priceElement = document.getElementById('price-' + itemName);

            if (!quantityElement || !priceElement) {
                console.error(`Element not found for item: ${itemName}`);
                return; // Exit if any element is missing
            }

            const quantity = parseInt(quantityElement.innerText);
            const price = parseFloat(priceElement.getAttribute('data-base-price'));
            
            // Calculate total price for this item and accumulate
            itemTotal += quantity * price;
            taxAmount = (0.1*itemTotal);
            finalTotal = itemTotal + taxAmount + deliveryCharges;

        });
        let savedFinalTotal = finalTotal;
        console.log("total:",savedFinalTotal);

        // Update the displayed item total in the checkout container
        document.getElementById('itemTotal').innerText = '$' + itemTotal.toFixed(2);
        document.getElementById('taxAmount').innerText = '$' + taxAmount.toFixed(2);
        document.getElementById('finalTotal').innerText = '$' + finalTotal.toFixed(2);
        // Example: Display savedFinalTotal in a different part of the page
        document.getElementById('anotherTotalDisplay').value = savedFinalTotal.toFixed(2); // Save value to input
        console.log("Total Amount so far: ", itemTotal);
    }

    // Function to save the quantity in localStorage
    function saveQuantityToLocalStorage(item, quantity) {
        localStorage.setItem('cartItem-' + item, quantity);
    }

    // Function to load the quantity from localStorage
    function loadQuantityFromLocalStorage(item) {
        let savedQuantity = localStorage.getItem('cartItem-' + item);
        if (savedQuantity) {
            return parseInt(savedQuantity);
        }
        return 1; // Default quantity if not found
    }

    // Load saved quantities on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.item-quantity').forEach(quantityElement => {
            let item = quantityElement.getAttribute('id').replace('quantity-', '');
            let savedQuantity = loadQuantityFromLocalStorage(item);
            quantityElement.innerText = savedQuantity;

            // Update the price based on the saved quantity
            let priceElement = document.getElementById('price-' + item);
            let basePrice = parseFloat(priceElement.getAttribute('data-base-price'));
            let newPrice = (basePrice * savedQuantity).toFixed(2);
            priceElement.innerText = '$ ' + newPrice;
        });

        // Initial call to update the checkout total
        updateCheckoutTotal(); // Ensure checkout total is set correctly on load
    });

    // Add event listeners for the buttons
    document.querySelectorAll('.btn-increase').forEach(button => {
        button.addEventListener('click', function() {
            let item = this.getAttribute('data-item');
            updateQuantity(item, 'increase');
        });
    });

    document.querySelectorAll('.btn-decrease').forEach(button => {
        button.addEventListener('click', function() {
            let item = this.getAttribute('data-item');
            updateQuantity(item, 'decrease');
        });
    });

    // Function to update and check final total
    function setFinalTotal() {
        var finalTotal = parseFloat(document.getElementById('finalTotal').innerText.replace('$', ''));
        var checkoutButton = document.getElementById('checkoutButton');
        var applyCoupon = document.getElementById('applyCoupon');
        var showCoupon = document.getElementById('showCoupon');

        // If final total is $0.00, disable the checkout button
        if (finalTotal === 0.00) {
            checkoutButton.disabled = true;
            showCoupon.disabled = true;
            applyCoupon.disabled = true;
        } 
        else {
            checkoutButton.disabled = false;
            showCoupon.disabled = false;
            applyCoupon.disabled = false;
        }

        // You can set the value to the hidden input if needed
        document.getElementById('anotherTotalDisplay').value = finalTotal;
    }

    // Run the check on page load to ensure the button is disabled if the total is $0.00
    window.onload = setFinalTotal;

    // You can also run this check every time a value is updated (e.g., after applying a coupon or changing quantity)
    document.getElementById('applyCoupon').addEventListener('click', setFinalTotal);
</script>