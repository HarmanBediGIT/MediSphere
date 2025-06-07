<?php
    // Start a session
    session_start();

    // $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

    // Database connection
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
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update quantity and price
        if (isset($_POST['update_qty'], $_POST['prod_code'], $_POST['quantity'])) {
            $prod_code = $_POST['prod_code'];
            $new_qty = intval($_POST['quantity']);

            // Fetch unit price (base price per product)
            $stmt = $conn->prepare("SELECT price FROM products WHERE prod_code = ?");
            $stmt->bind_param("s", $prod_code);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $unit_price = $row['price'];

            // Calculate new total price
            $new_total_price = $unit_price * $new_qty;

            // Update cart with new quantity and total price
            $updateStmt = $conn->prepare("UPDATE cart SET product_qty = ?, product_price = ? WHERE user_id = ? AND product_id = ?");
            $updateStmt->bind_param("idis", $new_qty, $new_total_price, $user_id, $prod_code);
            $success = $updateStmt->execute();

            if ($success) {
                echo "success";
            } else {
                echo "error";
            }
            exit;
        }

        // Remove item logic
        if (isset($_POST['remove_item'], $_POST['prod_code'])) {
            $prod_code = $_POST['prod_code'];
            $deleteStmt = $conn->prepare("DELETE FROM cart WHERE product_id = ? AND user_id = ?");
            $deleteStmt->bind_param("ss", $prod_code, $user_id);
            $success = $deleteStmt->execute();
            echo $success ? "success" : "error";
            exit;
        }
    }

    $cartItems = [];
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $cartItems[] = $row;
            }
        } else {
            echo "Error retrieving cart items.";
        }
    } 

    $cartCount = 0;
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $countStmt->bind_param("i", $user_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        if ($countRow = $countResult->fetch_assoc()) {
            $cartCount = $countRow['count'];
        }
    }

    // else {
    //     echo "User not logged in.";
    // }

    $notify_count = 0;
    // Count messages in contact table
    $contactCountResult = $conn->query("SELECT COUNT(*) AS total FROM contact");
    $contactCountRow = $contactCountResult->fetch_assoc();
    $contactCount = $contactCountRow['total'] ?? 0;

    // Count pending tickets in tickets table (assuming a 'status' column exists)
    $ticketsCountResult = $conn->query("SELECT COUNT(*) AS total FROM tickets");
    $ticketsCountRow = $ticketsCountResult->fetch_assoc();
    $pendingTicketCount = $ticketsCountRow['total'] ?? 0;

    // Combined count
    $notify_count = $contactCount + $pendingTicketCount;
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
                <div class="logo" style="text-align: center;">
                    <a href="home.php">
                        <img src="images/medisphere.png" style="width: 60px; display: block; margin: 0 auto;">
                    </a>
                    <div style="color: white; font-size: 16px; margin-top: 5px;">MediSphere</div>
                </div>

            <input type="checkbox" id="click">
            <label for="click" class="menu-btn">
                <i class="fas fa-bars"></i>
            </label>

            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="products.php">Products</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="categories.php">Categories</a></li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="orders.php">Order Status</a></li>
                    <?php endif; ?>

                    <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="pending_tickets.php"><i class="fas fa-bell"></i><span id="cartCountBadge" style="
                            position: absolute;
                            top: -8px;
                            right: -10px;
                            background: red;
                            color: white;
                            border-radius: 50%;
                            padding: 3px 10px;
                            font-size: 12px;
                            font-weight: bold;
                            display: <?php echo ($notify_count > 0 ? 'inline-block' : '1'); ?>;
                        ">
                            <?php echo $notify_count; ?>
                        </span></a></li>
                    <?php else: ?>
                        <li><a href="ticketraisingpage.php"><i class="fas fa-bell"></i></a></li>
                    <?php endif; ?>

                <?php else: ?>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="loginpage.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div id="cartButton" class="cart-button" onclick="openCart()" style="position: relative;">
            <i class="fas fa-shopping-cart"></i>
            <span id="cartCountBadge" style="
                position: absolute;
                top: -8px;
                right: -10px;
                background: red;
                color: white;
                border-radius: 50%;
                padding: 3px 10px;
                font-size: 12px;
                font-weight: bold;
                display: <?php echo ($cartCount > 0 ? 'inline-block' : '0'); ?>;
            ">
                <?php echo $cartCount; ?>
            </span>
        </div>

        
        <div class="container">
            <div class="profile-container">
                <h1>My Cart</h1>
            </div>
            <?php 
                // Fetch all items added to the cart by the logged-in user
                $sql = "SELECT * FROM cart WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $itemName = $row['product_name'];
                        $unitPrice = $row['product_price'];
                        $itemPrice = $row['product_price']*$row['product_qty'];
                        $itemQty = $row['product_qty'];
                        $itemSize = $row['product_size'];
                        $itemColor = $row['product_color'];
                        $itemMaterial = $row['product_material'];
                        $itemManufacturer = $row['product_manufacturer'];
                        $itemCode = $row['product_id'];

                            // Set up default values for taxes and delivery charges
                            $taxRate = 0.10; // 10% tax
                            $deliveryCharges = 5.00; // Example fixed delivery charges
                            $finalAmount = 0; // Initialize final amount

                        ?>

                        <!-- Cart Item Display -->
                        <div class="profile-container" style="display: flex; align-items: center; justify-content: space-between; background-color: #f0f8f8; padding: 15px; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                            <!-- Product Image -->
                            <div style="flex: 0 0 auto; margin-right: 15px;">
                                <img src="images/<?php echo htmlspecialchars($itemName); ?>.png" alt="<?php echo htmlspecialchars($itemName); ?>" style="width: 100%; height: auto; border-radius: 5px; margin-right:-300px;">
                            </div>

                            <!-- Product Details -->
                            <div class="profile-details">
                                <h3><?php echo htmlspecialchars($itemName); ?></h3>
                                <h4>Price : $<?php echo number_format((float)$unitPrice, 2, '.', ''); ?></h4>
                                <p>Size : <?php echo htmlspecialchars($itemSize); ?></p>
                                <p>Color : <?php echo htmlspecialchars($itemColor); ?></p>
                                <p>Material : <?php echo htmlspecialchars($itemMaterial); ?></p>
                                <p>Manufacturer : <?php echo htmlspecialchars($itemManufacturer); ?></p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="quantity-controls" style="display:flex; text-align:center; align-items:center;" data-prod-code="<?= $row['product_id'] ?>" >
                                <button class="qty-decrease btn btn-sm btn-outline-secondary" style="font-size: 20px; background-color: #e0f2f2; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">-</button>
                                <div id="quantity-<?= htmlspecialchars($row['product_id']) ?>" class="item-quantity" style="width: 40px; font-size: 18px;"> <?= htmlspecialchars($row['product_qty'])?> </div>
                                <input type="hidden" class="item-price" value="<?= htmlspecialchars($unitPrice)?>"> </input>
                                <button class="qty-increase btn btn-sm btn-outline-secondary" style="font-size: 20px; background-color: #e0f2f2; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">+</button>
                            </div>
                        </div>

                        <?php
                    }
                } else {
                    ?>
                    <div class="empty-cart-container">
                        <img src="images/cart.png" alt="Empty Cart" class="empty-cart-img">
                        <h2 class="empty-cart-text">Your Cart is Empty</h2>
                        <p class="empty-cart-description">It looks like you haven't added any items to your cart yet.</p>
                    </div>
                    <?php
                    $totalAmount = 0; // No total amount if cart is empty
                    $deliveryCharges = 0.00;
                }
            ?>

            
            <div class="checkout-container">
                <h2>Checkout</h2>
                <?php
                    $discount = 0;
                    $item_total = 0;
                    foreach ($cartItems as $item) {
                        $price = (float)$item['product_price'];
                        // $qty = isset($item['product_qty']) ? (int)$item['product_qty'] : 1;
                        $item_total += $price;
                    }
                    $tax = $item_total * 0.10;
                    // $delivery = 0;
                    $available_coupons = [];

                    // Fetch available coupons where coupon_amount < item_total
                    $couponStmt = $conn->prepare("SELECT * FROM coupons WHERE amount < ?");
                    $couponStmt->bind_param("d", $item_total);
                    $couponStmt->execute();
                    $couponResult = $couponStmt->get_result();
                    while ($row = $couponResult->fetch_assoc()) {
                        $available_coupons[] = $row;
                    }

                    $final_total = $item_total + $tax + $deliveryCharges - $discount;
                ?>
                <div class="checkout-details">
                    <div class="checkout-item">
                        <label>Item Total : </label>
                        <span id="item-total">$<?= number_format($item_total, 2) ?></span> <!-- Dynamic item total -->
                    </div>
                    <div class="checkout-item">
                        <label>Taxes (10%) : </label>
                        <span id="tax">$<?= number_format($tax, 2) ?></span> <!-- Dynamic tax total -->
                    </div>
                    <div class="checkout-item">
                        <label class="popover-button-default" data-content=""  title="Delivery Charges Breakdown" data-trigger="hover" data-placement="left">
                            Delivery Charges : <i class="glyph-icon icon-info-circle" style="float: right; font-size: 15px; text-align: center;"></i>
                        </label>
                        <span id="deliveryChargesAmount">$<?php echo number_format($deliveryCharges, 2); ?></span>
                    </div>
                    <div class="checkout-item"> 
                        <!-- <label>Coupon Code : </label> -->
                        <input type="hidden" id="couponCode" name="coupon_code" placeholder="Enter coupon code">
                        <!-- <button id="showCoupon" class="apply-btn" data-toggle="modal" data-target="#couponModal">Show Coupons</button>  -->
                        <!-- <button id="applyCoupon" class="apply-btn">Apply</button> -->
                    </div>
                    <!-- <div class="checkout-item">
                        <label>Discount : </label>
                        <span id="discountVal" style="color:#4caf50;">-$<?php echo number_format(0, 2); ?></span> 
                    </div> -->
                    <div class="checkout-item total-amount">
                        <label>Final Total : </label>
                        <span id="finalTotal">$<?= number_format($final_total, 2) ?></span> <!-- Final total will be calculated later -->
                    </div>
                    <div class="checkout-action">
                        <form action="process_checkout.php" method="POST" id="checkoutForm">
                            <input type="hidden" id="anotherTotalDisplay" name="anotherTotalDisplay" readonly>
                            <button type="submit" id="checkoutButton" class="checkout-btn" onclick="setFinalTotal()" style="cursor: not-allowed;" disabled>Proceed Checkout</button>
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
                                <?php foreach ($available_coupons as $coupon): ?>
                                    <li class="list-group-item list-group-item-action coupon-item"
                                        data-id="<?= $coupon['id'] ?>"
                                        data-code="<?= $coupon['coupon_code'] ?>"
                                        data-amount="<?= $coupon['amount'] ?>">
                                        <?= $coupon['coupon_code'] ?> - $<?= number_format($coupon['amount'], 2) ?> off
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Apply</button>
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
                <a href="#">
                    <i class="fa-brands fa-square-facebook"></i>
                </a>
            </div>
            <div class="copyright">
                © Copyright MediSphere 2025, All rights reserved
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    <script>
        $(document).ready(function () {
            $('.qty-increase').click(function () {
                const container = $(this).closest('.quantity-controls');
                const prodCode = container.data('prod-code');
                const qtyDiv = container.find('.item-quantity');
                const currentQty = parseInt(qtyDiv.text());

                $.post('cart.php', {
                    update_qty: true,
                    prod_code: prodCode,
                    quantity: currentQty + 1
                    }, function (response) {
                    if (response.trim() === 'success') {
                        qtyDiv.text(currentQty + 1);
                        location.reload(); // Update totals if needed
                    }
                });
            });

            $('.qty-decrease').click(function () {
                const container = $(this).closest('.quantity-controls');
                const prodCode = container.data('prod-code');
                const qtyDiv = container.find('.item-quantity');
                const currentQty = parseInt(qtyDiv.text());

                if (currentQty === 1) {
                    if (confirm('Do you want to remove the product?')) {
                        $.post('cart.php', {
                            remove_item: true,
                            prod_code: prodCode
                            }, function (response) {
                            if (response.trim() === 'success') {
                                location.reload();
                            }
                        });
                    }
                } else {
                    $.post('cart.php', {
                        update_qty: true,
                        prod_code: prodCode,
                        quantity: currentQty - 1
                    }, function (response) {
                        if (response.trim() === 'success') {
                        qtyDiv.text(currentQty - 1);
                        location.reload();
                        }
                    });
                }
            });

            // Handle coupon click from modal
            $('#couponList').on('click', '.coupon-item', function () {
                $('#couponModal').modal('hide');

                const code = $(this).data('code');
                const amount = parseFloat($(this).data('amount'));

                // Set coupon code in input
                $('#couponCode').val(code);

                // Apply discount immediately
                applyDiscount(amount);
            });

            // Optional: If user clicks "Apply" manually after typing code (no validation logic here)
            $('#applyCoupon').on('click', function () {
                const enteredCode = $('#couponCode').val().trim();
                const matchedItem = $(`#couponList .coupon-item`).filter(function () {
                    return $(this).data('code') === enteredCode;
                });

                if (matchedItem.length > 0) {
                    const amount = parseFloat(matchedItem.data('amount'));
                    applyDiscount(amount);
                } else {
                    alert("Invalid coupon code");
                }
            });
        });

        function applyDiscount(discount) {
                const itemTotal = parseFloat($('#item-total').text().replace('$', '')) || 0;
                const tax = parseFloat($('#tax').text().replace('$', '')) || 0;
                const delivery = parseFloat($('#deliveryChargesAmount').text().replace('$', '')) || 0;

                const finalAmount = itemTotal + tax + delivery - discount;

                $('#discountVal').text(`-$${discount.toFixed(2)}`);
                $('#finalTotal').text(`$${finalAmount.toFixed(2)}`);
            }


            function setFinalTotal() {
                const finalTotal = parseFloat($('#finalTotal').text().replace('$', '')) || 0;
                const couponCode = $('#couponCode').val().trim();

                $('#checkoutForm').find('input[name="final_total"]').remove();
                $('#checkoutForm').find('input[name="applied_coupon_code"]').remove();

                $('<input>').attr({
                    type: 'hidden',
                    name: 'final_total',
                    value: finalTotal.toFixed(2)
                }).appendTo('#checkoutForm');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'applied_coupon_code',
                    value: couponCode
                }).appendTo('#checkoutForm');

                const checkoutButton = document.getElementById('checkoutButton');
                if (finalTotal === 0) {
                    checkoutButton.disabled = true;
                    checkoutButton.style.cursor = 'not-allowed';
                } 
                else {
                    checkoutButton.disabled = false;
                    checkoutButton.style.cursor = 'pointer';
                }
            }

        // Run the check on page load to ensure the button is disabled if the total is $0.00
        window.onload = function () {
            setFinalTotal();
        };
    </script>
