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

    if (!isset($_GET['prod_code'])) {
        die("Product code not provided!");
    }
    
    $prod_code = htmlspecialchars($_GET['prod_code']); 
    
    $query = "SELECT * FROM products WHERE prod_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $prod_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } 
    else {
        die("Product not found!");
    }
    // Get the product ID from the URL
    // $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    // $code = isset($_GET['code']) ? $_GET['code'] : '';

    // if ($product_id > 0) {
    //     // Fetch the product details from the database
    //     $sql = "SELECT * FROM products WHERE id = ?";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bind_param("i", $product_id);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
        
    //     if ($result->num_rows > 0) {
    //         $product = $result->fetch_assoc();
    //         // $row = $result->fetch_assoc();
    //         // $code = $row['code'];
    //     } 
    //     else {
    //         header("Location: products.php");
    //         exit();
    //     }
    // } 
    // else {
    //     header("Location: products.php");
    //     exit();
    // }

    // Fetch reviews and ratings data
    $sql = "SELECT rating, review_text, COUNT(*) as count FROM reviews WHERE product_code = ? GROUP BY rating";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $reviews = $stmt->get_result();

    // Initialize variables for rating calculations
    $totalReviews = 0;
    $ratingSum = 0;
    $ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]; // Store counts for each rating

    // Fetch reviews data and calculate totals
    $reviewTexts = []; // Array to store reviews with their ratings
    $totalReviews = 0;
    $ratingSum = 0;

    while ($row = $reviews->fetch_assoc()) {
        $rating = intval($row['rating']);
        $count = intval($row['count']);
        
        // Update rating summary
        $ratings[$rating] = $count;
        $totalReviews += $count;
        $ratingSum += $rating * $count;
        
        // Store each review with its text and rating
        $reviewTexts[] = [
            'text' => htmlspecialchars($row['review_text']), // Store review text safely
            'rating' => $rating // Store rating
        ];
    }

    // Calculate average rating
    $averageRating = $totalReviews > 0 ? $ratingSum / $totalReviews : 0;

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
    <title>Products</title>

        <script src="https://www.paypal.com/sdk/js?client-id=ARlGd7ULiv78kwQKZdCqFDMgWo37rwP-bW5mEUZZNe96q5DR-DYIp2zJDnZfJ2O9ky6uBpxqARj9_DHB&currency=USD"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <li><a class="active" href="products.php">Products</a></li>

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

        <div id="cartButton" class="cart-button" onclick="openCart()">
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

        <!-- Products Grid -->
        <div class="product-details-container">
            <div class="product-gallery">
                <?php
                    $productName = htmlspecialchars($product['name']);
                    $mainImage = "images/{$productName}.png";

                    if (!file_exists($mainImage)) {
                        $mainImage = "images/default.jpg";
                    }
                ?>
                <div class="main-image">
                    <img src="<?php echo $mainImage; ?>" alt="<?php echo $productName; ?>">
                </div>
                <div class="thumbnail-images">
                    <?php
                        // Show the main image as the first thumbnail
                        if (file_exists("images/{$productName}.png")) {
                            echo '<img src="images/' . $productName . '.png" alt="' . $productName . '">';
                        }

                        // Show remaining images with _2 to _4
                        for ($i = 2; $i <= 4; $i++) {
                            $imagePath = "images/{$productName}_{$i}.png";
                            if (file_exists($imagePath)) {
                                echo '<img src="' . $imagePath . '" alt="' . $productName . '">';
                            }
                        }
                    ?>
                </div>
                <br>
                <?php
                    // Check the quantity and display the appropriate message
                    if ($product['qty'] > 12) {
                        echo '<div class="stock-message" style="color: green; font-size:30px;"><b>Available In Stock</b></div>';
                    } 
                    else if ($product['qty'] == 0) {
                        echo '<div class="stock-message" style="color: red; font-size:30px;"><b>Out of Stock</b></div>';
                    } 
                    else {
                        echo '<div class="stock-message" style="color: red; font-size:30px;"><b>HURRY! Only ' . htmlspecialchars($product['qty']) . ' left</b></div>';
                    }
                ?>
            </div>

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <span class="discount" style="color:inherit;"><?php echo htmlspecialchars($product['description']); ?></span>
                <br><br>
                <div class="price">
                    <span class="actual-price">$<?php echo htmlspecialchars($product['price']); ?></span>
                    <br>
                    <span class="discount">Discount : 10%</span> <!-- Sample, could be changed as per the  requirement-->                
                </div>

                <div class="options">
                    <?php if (!empty($product['sizes'])): ?>
                        <h3>Available Sizes :</h3>
                        <div class="size-options">
                            <?php
                                // Split the sizes string into an array and display each size as a button
                                $sizes = explode(',', $product['sizes']); 
                                foreach ($sizes as $size) {
                                    $size = trim($size); // Remove any extra spaces
                                    echo '<button class="size-button" data-size="' . htmlspecialchars($size) . '">' . htmlspecialchars($size) . '</button>';
                                }
                            ?>
                        </div>
                        <br>
                    <?php endif; ?>

                    <?php if (!empty($product['colors'])): ?>
                        <h3>Colors Available : </h3>
                        <div class="color-options">
                            <?php
                                // Split the colors string into an array and display each as a colored circle
                                $colors = explode(',', $product['colors']); 
                                foreach ($colors as $color) {
                                    $color = trim($color); // Remove any extra spaces
                                    echo '<span class="color" style="background-color: ' . htmlspecialchars($color) . ';" data-color="' . htmlspecialchars($color) . '"></span>';
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="description">
                    <h3>Detailed description of the Product : </h3>
                    <ul>
                        <li><strong>Size : </strong> <span id="selectedSize"><?php echo !empty($product['size']) ? htmlspecialchars($product['size']) : '--'; ?></span></li>
                        <li><strong>Color : </strong> <span id="selectedColor"><?php echo !empty($product['color']) ? htmlspecialchars($product['color']) : '--'; ?></span></li>

                        <?php if (!empty($product['material'])): ?>
                            <li><strong>Material : </strong> <?php echo htmlspecialchars($product['material']); ?></li>
                        <?php endif; ?>

                        <?php if (!empty($product['manufacturer'])): ?>
                            <li><strong>Manufacturer : </strong> <?php echo htmlspecialchars($product['manufacturer']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="action-buttons">
                    <?php 
                        $isDisabled = $product['qty'] == 0 ? 'disabled style="cursor: not-allowed; pointer-events: none; opacity: 0.5;"' : '';

                        echo '<a href="#" class="add-to-cart" ' . $isDisabled . '
                            data-product-id="' . htmlspecialchars($product['prod_code']) . '" 
                            data-product-name="' . htmlspecialchars($product['name']) . '" 
                            data-product-size="' . htmlspecialchars($product['sizes']) . '" 
                            data-product-color="' . htmlspecialchars($product['colors']) . '" 
                            data-product-material="' . htmlspecialchars($product['material']) . '" 
                            data-product-manufacturer="' . htmlspecialchars($product['manufacturer']) . '" 
                            data-product-price="' . htmlspecialchars($product['price']) . '" 
                            data-product-qty="' . htmlspecialchars($product['qty']) . '">
                            Add to Cart
                        </a>';
                    ?>

                    <button class="buy-now"
                            data-id="<?= $product['prod_code'] ?>"
                            data-name="<?= $product['name'] ?>"
                            data-price="<?= $product['price'] ?>"
                            <?= $product['qty'] == 0 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                        Buy Now
                    </button>

                    <div id="paypal-buy-now-container" style="margin-top: 20px;"></div>

                    <div id="cart-message" style="
                        display: none;
                        padding: 12px 20px;
                        background-color: #e0ffe0;
                        color: #007700;
                        border: 1px solid #b2e0b2;
                        border-radius: 5px;
                        text-align: center;
                        margin: 10px auto;
                        max-width: 400px;
                        font-weight: bold;
                        z-index: 9999;
                    "></div>
                </div>
            </div>
        </div>
        
        <div class="customer-reviews">
            <br>
            <div class="reviews-container">
                <!-- Left side: Average Rating -->
                <div class="average-rating-section">
                    <div class="average-rating-stars">
                        <?php
                            // Logic to calculate average rating
                            $filledStars = floor($averageRating);
                            $halfStar = ($averageRating - $filledStars >= 0.5);

                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $filledStars) {
                                    echo '<i class="fa fa-star filled-star"></i>'; // Filled star
                                } 
                                elseif ($i === $filledStars + 1 && $halfStar) {
                                    echo '<i class="fa fa-star-half-alt filled-star"></i>'; // Half star
                                } 
                                else {
                                    echo '<i class="fa fa-star empty-star"></i>'; // Empty star
                                }
                            }
                        ?>
                        <span><?php echo number_format($averageRating, 1); ?> out of 5</span>
                    </div>
                    <br>
                    <p><?php echo $totalReviews; ?> customer reviews</p>
                </div>

                <!-- Right side: Rating Breakdown -->
                <div class="rating-breakdown-section">
                    <ul>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <?php echo $i;?> 
                                    <span style="width: 150px;">
                                        <?php for ($j = 1; $j <= $i; $j++): ?>
                                            <i class="fa fa-star filled-star"></i>
                                        <?php endfor; ?>
                                        <?php for ($j = 1; $j <= (5 - $i); $j++): ?>
                                            <i class="fa fa-star empty-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                <div class="rating-bar" style="width: 70%; height: 10px; background-color: #eee; border-radius: 5px; position: relative;">
                                    <div class="rating-fill" style="background-color: #ffc107; width: <?php echo $totalReviews > 0 ? ($ratings[$i] / $totalReviews) * 100 : 0; ?>%; height: 100%; border-radius: 5px;">
                                    </div>
                                </div>
                                <span style="margin-left: 0;">
                                    <?php echo $totalReviews > 0 ? round(($ratings[$i] / $totalReviews) * 100, 1) : 0; ?>%
                                </span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
            <br>
            <h2>Other Customers' Reviews : </h2>
                <?php if (count($reviewTexts) > 0): ?>
                    <ul class="review-list">
                        <?php foreach ($reviewTexts as $index => $review): ?>
                            <li class="review-item">
                                <div class="review-number"><?php echo $index + 1; ?></div>
                                <div class="review-content">
                                    <p><?php echo htmlspecialchars($review['text']); ?></p>
                                    <p>Rating : <?php echo $review['rating'];?>/5 &ensp;
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <?php if ($i < $review['rating']): ?>
                                                <span class="fa fa-star filled-star"></span>
                                            <?php else: ?>
                                                <span class="fa fa-star empty-star"></span>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </p>  <!-- This will appear on the next line (star ratings will appear on next line after the review text)-->
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No customer reviewed this product. <br> Be the first one to review it!</p>
                <?php endif; ?>
        </div>

        <div class="customer-reviews">
            <h2>Share Your Reviews</h2>
            <form id="reviewForm" method="post" action="submit_review.php"> <!-- Added form tag -->
                <div class="review-container" style="display: flex; justify-content: space-between;">
                    <!-- Left Section : Star Rating & Slider -->
                    <div class="rating-section" style="flex: 1; padding: 20px;">
                        <h3>Rate this product:</h3>
                        <div id="starRating" class="star-rating" style="font-size: 24px;">
                            <i class="fa fa-star empty-star" data-value="1"></i>
                            <i class="fa fa-star empty-star" data-value="2"></i>
                            <i class="fa fa-star empty-star" data-value="3"></i>
                            <i class="fa fa-star empty-star" data-value="4"></i>
                            <i class="fa fa-star empty-star" data-value="5"></i>
                        </div>
                        <input type="range" id="ratingSlider" name="rating" min="0" max="5" value="0" step="1" style="width: 100%; margin-top: 10px;">
                        <span id="sliderValue" style="font-size: 16px;">Rating : 0/5</span>
                    </div>

                    <!-- Right Section: Review Text Area -->
                    <div class="review-section" style="flex: 1.5; padding: 20px;">
                        <h3>Write your review : </h3>
                        <textarea id="reviewText" name="review_text" placeholder="Leave your review here..." style="width: 100%; height: 150px; padding: 10px; font-size: 16px;" required></textarea>
                        <input type="hidden" name="product_code" value="<?php echo $code; ?>"> <!-- Hidden field for product code -->
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"> <!-- Hidden field for user id -->
                        <button
                            type="submit"
                            id="submitReview"
                            style="margin-top: 10px; padding: 10px 20px; background-color: #f0c14b; border: 1px solid #a88734; cursor: pointer; font-size: 16px;"
                            onmouseover="this.style.backgroundColor='#ddb347'; this.style.borderColor='#a67634';"
                            onmouseout="this.style.backgroundColor='#f0c14b'; this.style.borderColor='#a88734';"
                            >
                            Give Review
                        </button>
                    </div>
                </div>
            </form>
        </div>

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
                &copy; Copyright 2023, All rights reserved
            </div>
        </div>
    </body>
</html>

<script>
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            // Get selected size and color
            const selectedSizeBtn = document.querySelector('.size-button.selected');
            const selectedColorCircle = document.querySelector('.color.selected');

            if (!selectedSizeBtn || !selectedColorCircle) {
                alert("Please select a size and a color before adding to cart.");
                return;
            }

            const selectedSize = selectedSizeBtn.getAttribute('data-size');
            const selectedColor = selectedColorCircle.getAttribute('data-color');

            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productMaterial = this.getAttribute('data-product-material');
            const productManufacturer = this.getAttribute('data-product-manufacturer');
            const productPrice = parseFloat(this.getAttribute('data-product-price'));
            const productQty = parseInt(this.getAttribute('data-product-qty'), 10);

            // Send AJAX request to add to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_name: productName,
                    product_size: selectedSize,
                    product_color: selectedColor,
                    product_material: productMaterial,
                    product_manufacturer: productManufacturer,
                    product_price: productPrice,
                    product_qty: productQty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showCartMessage(data.message, data.status);

                    if (data.status === "success") {
                        setTimeout(() => {
                            location.reload(); // ✅ Reload the page after a short delay
                        }, 1000); // optional: delay reload for 1 second to show message
                    }
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
    });

    function showCartMessage(message, status) {
        const msgDiv = document.getElementById('cart-message');
        msgDiv.textContent = message;

        if (status === 'success') {
            msgDiv.style.backgroundColor = '#e0ffe0';
            msgDiv.style.color = '#007700';
            msgDiv.style.border = '1px solid #b2e0b2';
        } else {
            msgDiv.style.backgroundColor = '#ffe0e0';
            msgDiv.style.color = '#770000';
            msgDiv.style.border = '1px solid #e0b2b2';
        }

        msgDiv.style.display = 'block';

        setTimeout(() => {
            msgDiv.style.opacity = '1';
            msgDiv.style.transition = 'opacity 0.5s ease';
        }, 10);

        setTimeout(() => {
            msgDiv.style.opacity = '0';
            setTimeout(() => {
                msgDiv.style.display = 'none';
                msgDiv.style.opacity = '1'; // reset for next message
            }, 600);
        }, 2500);
    }

    function openCart() {
        window.location.href = "cart.php"; // Redirect to cart page
    }

    $(document).ready(function() {
        // Check if size buttons exist before binding events
        if ($('.size-button').length > 0) {
            $('.size-button').on('click', function() {
                // Remove selected class from all buttons
                $('.size-button').removeClass('selected');
                // Add selected class to the clicked button
                $(this).addClass('selected');
                // Update the selected size in the description
                $('#selectedSize').text($(this).data('size'));
            });
        }

        // Check if color circles exist before binding events
        if ($('.color').length > 0) {
            $('.color').on('click', function() {
                // Remove selected class from all colors
                $('.color').removeClass('selected');
                // Add selected class to the clicked color
                $(this).addClass('selected');
                // Update the selected color in the description
                $('#selectedColor').text($(this).data('color'));
            });
        }

        // Check if thumbnail images exist before binding events
        if ($('.thumbnail-images img').length > 0) {
            $('.thumbnail-images img').on('click', function() {
                var newSrc = $(this).attr('src');
                $('.main-image img').attr('src', newSrc);
            });
        }
    });

    const starRating = document.querySelectorAll('.star-rating .fa-star');
    const ratingSlider = document.getElementById('ratingSlider');
    const sliderValue = document.getElementById('sliderValue');

    // Function to update stars based on slider value
    function updateStars(rating) {
        starRating.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('empty-star');
                star.classList.add('filled-star');
            } 
            else {
                star.classList.remove('filled-star');
                star.classList.add('empty-star');
            }
        });
    }

    // Event listener for the slider
    ratingSlider.addEventListener('input', function() {
        const rating = parseInt(this.value);
        sliderValue.textContent = `Rating : ${rating}/5`;
        updateStars(rating);
    });

    document.querySelectorAll('.buy-now').forEach(button => {
        button.addEventListener('click', function () {
            const selectedSizeBtn = document.querySelector('.size-button.selected');
            const selectedColorCircle = document.querySelector('.color.selected');

            if (!selectedSizeBtn || !selectedColorCircle) {
                alert("Please select a size and a color before proceeding.");
                return;
            }

            const selectedSize = selectedSizeBtn.getAttribute('data-size');
            const selectedColor = selectedColorCircle.getAttribute('data-color');

            const productId = this.dataset.id;
            const productName = encodeURIComponent(this.dataset.name);
            const productPrice = this.dataset.price;

            // Redirect to address entry page with product info and selected options
            window.location.href = `add_address.php?product_id=${productId}&name=${productName}&price=${productPrice}&size=${encodeURIComponent(selectedSize)}&color=${encodeURIComponent(selectedColor)}`;
        });
    });

</script>