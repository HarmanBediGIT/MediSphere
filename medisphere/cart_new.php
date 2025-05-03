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
    } else {
        echo "User not logged in.";
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
                <li><a href="Home.php">Home</a></li>
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

                        ?>

                        <!-- Cart Item Display -->
                        <div class="profile-container" style="display: flex; align-items: center; justify-content: space-between; background-color: #f0f8f8; padding: 15px; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                            <!-- Product Image -->
                            <div style="flex: 0 0 auto; margin-right: 15px;">
                                <img src="images/products/<?php echo htmlspecialchars($itemCode); ?>.jpg" alt="<?php echo htmlspecialchars($itemName); ?>" style="width: 80px; height: auto; border-radius: 5px;">
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
                }

                // Set up default values for taxes and delivery charges
                $taxRate = 0.10; // 10% tax
                $deliveryCharges = 5.00; // Example fixed delivery charges
                $finalAmount = 0; // Initialize final amount
            ?>

            
            <!-- Checkout Container -->
            <div class="checkout-container">
                <h4>Checkout Summary</h4>
                <?php
                
                    $discount = 0;
                    $item_total = 0;

                    foreach ($cartItems as $item) {
                        $price = (float)$item['product_price'];
                        // $qty = isset($item['product_qty']) ? (int)$item['product_qty'] : 1;
                        $item_total += $price;
                    }

                    $tax = $item_total * 0.10;
                    $delivery = 5;
                    $available_coupons = [];

                    // Fetch available coupons where coupon_amount < item_total
                    $couponStmt = $conn->prepare("SELECT * FROM coupons WHERE amount < ?");
                    $couponStmt->bind_param("d", $item_total);
                    $couponStmt->execute();
                    $couponResult = $couponStmt->get_result();
                    while ($row = $couponResult->fetch_assoc()) {
                        $available_coupons[] = $row;
                    }

                    $final_total = $item_total + $tax + $delivery - $discount;
                ?>
                <div>
                    <p>Item Total: $<span id="item-total"><?= number_format($item_total, 2) ?></span></p>
                    <p>Tax (10%): $<span id="tax"><?= number_format($tax, 2) ?></span></p>
                    <p>Delivery Charges: $<span id="delivery"><?= number_format($delivery, 2) ?></span></p>
                    <div class="form-group">
                        <label for="coupon">Apply Coupon:</label>
                        <select id="coupon" class="form-control">
                            <option value="0" data-amount="0">-- Select Coupon --</option>
                            <?php foreach ($available_coupons as $coupon): ?>
                                <option value="<?= $coupon['id'] ?>" data-amount="<?= $coupon['amount'] ?>">
                                    <?= $coupon['coupon_code'] ?> - $<?= number_format($coupon['amount'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p>Discount: $<span id="discount">0.00</span></p>
                    <hr>
                    <h5>Total Payable: $<span id="final-total"><?= number_format($final_total, 2) ?></span></h5>
                    <button class="btn btn-success mt-2">Proceed to Checkout</button>
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
    });

</script>
