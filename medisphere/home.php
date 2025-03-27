<?php
    // Start a session
    session_start();

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

    $sql = "SELECT id, name, price FROM products WHERE price < 500 LIMIT 5";
    $result = $conn->query($sql);
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
                <li><a class="active" href="home.php">Home</a></li>
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

        <div id="cartButton" class="cart-button" onclick="openCart()">
            <i class="fas fa-shopping-cart"></i>
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
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <div class="product-item">
                                <?php
                                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                                    echo '<p class="price">$' . htmlspecialchars($row['price']) . '</p>';
                                    echo '<a href="#" class="add-to-cart-button" data-product-name="' . htmlspecialchars($row['name']) . '" data-product-price="' . htmlspecialchars($row['price']) . '">Add to Cart</a>';
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
            <h2>Limited Time Offers</h2>
            <div class="promo-banner">
                <!-- <img src="promo_banner.png" alt="Limited Time Offers"> </img> -->
                <div class="promo-text">
                    <h3>Up to 50% Off on Select Products!</h3>
                    <p>Get the best deals on essential medical equipment now.</p>
                    <br>
                    <a href="categories.php" class="cta-button">Shop Now</a>
                </div>
            </div>
        </section>
        
        <div class="footer1">
            <div class="social">
                <a href="#">
                    <i class="fa-brands fa-square-instagram"></i>
                </a>
                <a href="#">
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
                            window.location.href = `/product/${product.id}`;
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

    document.querySelectorAll('.add-to-cart-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default anchor behavior
            const productName = this.getAttribute('data-product-name');
            const productPrice = parseFloat(this.getAttribute('data-product-price'));       
            // Send AJAX request to add to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_name: productName,
                    product_price: productPrice
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle response (success or error)
                alert(data.message);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
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