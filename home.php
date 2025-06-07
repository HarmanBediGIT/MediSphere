<?php
    // Start a session
    session_start();

    // $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

    require 'db_conn.php';
    
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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

    $sql = "SELECT prod_code, name, price FROM products WHERE price < 500 LIMIT 5";
    $result = $conn->query($sql);

    $sql2 = "SELECT * FROM categories LIMIT 5";
    $result2 = mysqli_query($conn, $sql2);

    // Array of images
    $images = [
        "images/diag_category.png",
        "images/surg_category.png",
        "images/care_category.png",
        "images/ppe_category.png",
        "images/lab_category.png",
        "images/home_category.png",
        "images/rehab_category.png",
        "images/img_category.png",
        "images/cons_category.png",
        "images/emer_category.png",
        // Add more images as needed
    ];

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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Home</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
        <link rel="stylesheet" type="text/css" href="css\index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    </head>

    <body>
        <div id="progressBarContainer">
            <div id="progressBar"></div>
        </div>
        
        <nav>
                <div class="logo" style="display: flex; justify-content: space-between; align-items: center; padding: 0px;">
                    <div style="text-align: center;">
                        <a href="home.php">
                            <img src="images/medisphere.png" style="width: 60px; display: block; margin: 0 auto;">
                        </a>
                        <div style="color: white; font-size: 16px; margin-top: 5px;">MediSphere</div>
                    </div>

                    <?php if ($isLoggedIn): ?>
                        <div style="color: white; font-size: 20px; text-align: right; padding: 0px 50px;">
                            <h5 style="margin: 0;">Welcome <?php echo $name; ?></h5>
                        </div>
                    <?php endif; ?>
                </div>

            <input type="checkbox" id="click">
            <label for="click" class="menu-btn">
                <i class="fas fa-bars"></i>
            </label>

            <ul>
                <li><a class="active" href="home.php">Home</a></li>
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

        <!-- Chatbot Button -->
        <div id="chatbotButton" class="chatbot-button" onclick="toggleChatbot()">
            <i class="fas fa-robot"></i>
        </div>

        <!-- Chatbot Interface -->
        <div id="chatbot" class="chatbot-window">
            <div class="chatbot-header">
                <h3>MediSphere Chatbot</h3>
                <span class="close-btn" onclick="toggleChatbot()">&times;</span>
            </div>
            <div class="chat-window" id="chatWindow">
                <div id="chatHistory" class="chat-history"></div>
                <div class="chat-input">
                    <input type="text" id="userInput" placeholder="Type your message...">
                    <button id="sendBtn" onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>

        <!-- Heading content -->
        <div class="content1">
            <div class="overlay" style="background-color:white;"> <img src="images/banner-bg.png"> </img> </div>
            <div class="hero-content">
                <h1>Elevate Your Care with Premium Medical Equipment</h1> <br><br>
                <p>Explore our wide range of medical devices, designed to meet every need with precision and care.</p>
                <br> <br>
                <a href="categories.php" class="cta-button">Shop Now</a>
            </div>
        </div>

        <section id="about" class="about">
            <h2>About Us</h2>
            <p>"At MediSphere, we're not just providers — we're pioneers committed to enhancing healthcare through quality and innovation. Specializing in reliable, cutting-edge medical equipment, we deliver solutions that empower healthcare professionals to provide efficient, top-tier care. Our journey is driven by expertise, collaboration, and an unwavering dedication to improving patient outcomes. But this is only the start. Ready to see how MediSphere can transform your healthcare experience? Empowering healthcare, delivering quality."</p>
        </section>

        <section id="services" class="services">
            <h2>OUR SPECIALITY</h2>
            <div class="service-container">
                <div class="service-box">
                    <img src="images/spcl1.png" alt="Service 1">
                    <h3>Great Variety of Categories & Products</h3>
                </div>
                <div class="service-box">
                    <img src="images/spcl2.png" alt="Service 2">
                    <h3>Customer Centric and Satisfaction</h3>
                </div>
                <div class="service-box">
                    <img src="images/spcl3.png" alt="Service 3">
                    <h3>Continuous Improvement and Updates</h3>
                </div>
            </div>
        </section>

        <br><br>

            <!-- search bar implementation -->
            <div class="search-container">  <!-- Add position: relative to the parent container -->
                <form action="" method="POST" id="searchForm" autocomplete="off">
                    <input type="text" id="searchInput" placeholder="Search products..." name="search">
                    <button type="submit">Search</button>
                </form>
                <div id="searchResults" class="search-results"></div> <!-- Results will show here -->
            </div>

        <section class="featured-products">
            <div class="container">
                <h2>Featured Products</h2>
                <?php if ($result->num_rows > 0) { ?>
                    <div class="product-list">
                        <?php while ($row = $result->fetch_assoc()) { 
                            $imagePath = 'images/' . htmlspecialchars($row["name"]) . '.png';?>
                            <div class="product-item">
                            <?php
                                echo '<div class="product-image">';
                                    echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($row["name"]) . '">';
                                echo '</div>';
                                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                                echo '<p class="price">$' . htmlspecialchars($row['price']) . '</p>';
                                echo '<a class="show-prod" href="detailed_products.php?prod_code=' . urlencode($row['prod_code']) . '">View</a>';
                            ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p>No products found.</p>
                <?php } ?>
            </div>
        </section>

        <section class="promo">
            <h2>* LIMITED TIME OFFERS *</h2>
            <div class="promo-banner">
                <!-- <img src="promo_banner.png" alt="Limited Time Offers"> </img> -->
                <div class="promo-text">
                    <h3>Up to 50% Off on Selected Products!</h3>
                    <p>Get the best deals on essential medical equipment now.</p>
                    <br>
                    <!-- Category Cards Section -->
                    <div class="card-container">
                        <?php 
                        // Initialize a counter for images
                        $index = 0;

                        // Loop through the fetched categories
                        while ($row = mysqli_fetch_assoc($result2)): 
                            // Select an image based on the current index
                            $imagePath = $images[$index % count($images)];
                        ?>
                            <div class="card" style="width: 200px; height: 180px;" onclick="window.location.href='category_products.php?code=<?php echo urlencode($row['code']); ?>'">
                                <div class="card-inner">
                                    <div class="card-front" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                                        <img src="<?php echo $imagePath; ?>" style="width: 50%; height: 50%; object-fit: cover;" alt="Product Image" />
                                        <div class="category-name" style="margin-top: 10px; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($row['name']); ?>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="category-details">
                                            <div class="category-code">Unique Code: <?php echo htmlspecialchars($row['code']); ?></div>
                                            <div class="category-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                            <div class="category-code">Price Range: <?php echo htmlspecialchars($row['price_range']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php 
                            // Increment the index for the next image
                            $index++;
                        endwhile; 
                        ?>
                    </div>
                    <br>
                    <a href="categories.php" class="cta-button">View Other Categories</a>
                </div>
            </div>
        </section>
        
        <br><br>

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
    </body>
</html>
<script>
    window.addEventListener('scroll', function() {
        var nav = document.querySelector('nav');
        if (window.scrollY > 0) {
            nav.classList.add('scrolled'); // Apply gradient background
        } 
        else {
            nav.classList.remove('scrolled'); // Revert to transparent background
        }
    });

    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('searchInput').addEventListener('input', function () {
        const query = this.value;

        if (query.length > 0) {
            fetch('search_products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `search=${encodeURIComponent(query)}`
            })
            .then(response => response.json())
            .then(data => {
                const searchResults = document.getElementById('searchResults');
                searchResults.innerHTML = ''; // Clear previous results

                if (data.length > 0) {
                    // Calculate dynamic margin based on the number of results
                    const resultCount = data.length;
                    let dynamicMarginTop = 100 + (resultCount - 1) * 50; // Increment margin by 50px for each result
                    searchResults.style.marginTop = `${dynamicMarginTop}px`;

                    data.forEach(product => {
                        const resultDiv = document.createElement('div');
                        resultDiv.textContent = product.name;
                        resultDiv.addEventListener('click', () => {
                            window.location.href = `detailed_products.php?prod_code=${product.prod_code}`;
                        });
                        searchResults.appendChild(resultDiv);
                    });
                    searchResults.style.display = 'block'; // Make sure it shows up
                } 
                else {
                    searchResults.innerHTML = '<div>No products found</div>';
                    searchResults.style.marginTop = `100px`;
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } 
        else {
            document.getElementById('searchResults').innerHTML = ''; // Clear results if input is empty
            document.getElementById('searchResults').style.display = 'none'; // Hide container
        }
    });

    function openCart() {
        window.location.href = "cart.php"; // Redirect to cart page
    }

    function toggleChatbot() {
        const chatbot = document.getElementById("chatbot");
        chatbot.style.display = chatbot.style.display === "block" ? "none" : "block";
    }

    function sendMessage() {
        const userInput = document.getElementById("userInput").value;
        const chatHistory = document.getElementById("chatHistory");

        if (userInput.trim() !== "") {
            // Show User Message
            const userDiv = document.createElement("div");
            userDiv.classList.add("user-message");
            userDiv.textContent = userInput;
            chatHistory.appendChild(userDiv);

            // Show Loading Dots
            const loadingDiv = document.createElement("div");
            loadingDiv.classList.add("bot-message");
            loadingDiv.innerHTML = "Typing...";
            chatHistory.appendChild(loadingDiv);
            chatHistory.scrollTop = chatHistory.scrollHeight;

            // Clear Input
            document.getElementById("userInput").value = "";

            // Send AJAX Request
            fetch("chatbot_response.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `message=${encodeURIComponent(userInput)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.remove(); // Remove loading message

                const botDiv = document.createElement("div");
                botDiv.classList.add("bot-message");
                botDiv.textContent = data.response;
                chatHistory.appendChild(botDiv);
                chatHistory.scrollTop = chatHistory.scrollHeight;
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }
    }

</script>