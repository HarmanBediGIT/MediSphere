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
    } else {
        // User is logged in
        $isLoggedIn = true;
        // Access the user_id
        $user_id = $_SESSION['user_id'];
        $name = $_SESSION['username'];
        $role = $_SESSION['role'];
    }

    // Fetch user details from the database
    $sql = "SELECT user_id, user_name, password FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Check if the form to change password is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password == $confirm_password) {
            $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, 'si', $confirm_password, $user_id);
            mysqli_stmt_execute($update_stmt);

            header("Refresh: 2; url=profile.php");
            // echo "<script>alert('Password updated successfully!');</script>";
        } 
        else {
            header("Refresh: 2; url=profile.php");
            echo "<script>alert('Passwords do not match!');</script>";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
            
        // Sanitize and collect form inputs
        $user_name = $_POST['user_name'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phn = $_POST['phn'];
        $role = $_POST['role'];
    
        $sql = "INSERT INTO users (user_name, name, password, email, phn, role)
                VALUES ('$user_name', '$name', '$password', '$email', '$phn', '$role')";
    
        if ($conn->query($sql) === TRUE) {
            echo "<script>window.location.href='profile.php';</script>";
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $sql1 = "SELECT * from users";
    $result1 = mysqli_query($conn, $sql1);

    $sql2 = "SELECT * from users where role = 'customer'";
    $result2 = mysqli_query($conn, $sql2);

    $sql3 = "SELECT * from users where role = 'employee'";
    $result3 = mysqli_query($conn, $sql3);

    $sql4 = "SELECT * from users where role = 'admin'";
    $result4 = mysqli_query($conn, $sql4);

    $sql5 = "SELECT * from categories";
    $result5 = mysqli_query($conn, $sql5);

    $sql6 = "SELECT order_id, amount, created_at FROM payments WHERE user_id =  ?";
    $stmt6 = mysqli_prepare($conn, $sql6);
    mysqli_stmt_bind_param($stmt6, 'i', $user_id);
    mysqli_stmt_execute($stmt6);
    $result6 = mysqli_stmt_get_result($stmt6);

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
        
        <title>My Profile</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
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

                <?php if ($isLoggedIn): ?>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="categories.php">Categories</a></li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="orders.php">Order Status</a></li>
                    <?php endif; ?>

                    <li><a class="active" href="profile.php"><i class="fas fa-user"></i></a></li>
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

        <!-- <div id="cartButton" class="cart-button" onclick="openCart()" style="position: relative;">
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
        </div> -->

        
            <?php
                // Check if the logged-in user is an admin
                if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            ?>
            
            <br><br>
                <div class="stats">
                    <div class="stat-box">
                        <h3>TOTAL CUSTOMERS</h3>
                        <p> <?php echo $result2->num_rows ?> </p>
                    </div>
                    <div class="stat-box">
                        <h3>TOTAL EMPLOYEES</h3>
                        <p> <?php echo $result3->num_rows ?> </p>
                    </div>
                    <div class="stat-box">
                        <h3>TOTAL ADMINS</h3>
                        <p> <?php echo $result4->num_rows ?> </p>
                    </div>
                </div>

                <div class="filter-section">
                    <div class="dropdown">
                        <button class="filter-button">
                            <i class="fas fa-filter"></i> FILTER USERS
                        </button>
                        <div class="dropdown-content">
                            <a href="#" data-role="all">All</a>
                            <a href="#" data-role="customer">Customers</a>
                            <a href="#" data-role="employee">Employees</a>
                        </div>
                    </div>
                </div>

                <div class="profile-container" style="width:90%; display:block; text-align:center;">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <!-- <th>Username</th> -->
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <?php while ($row = $result1->fetch_assoc()) { ?>
                                <tr data-role="<?php echo strtolower($row['role']); ?>" id="user-<?php echo $row['user_id']; ?>" 
                                    data-user-id="<?php echo $row['user_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                    data-username="<?php echo htmlspecialchars($row['user_name']); ?>"
                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                    data-phn="<?php echo htmlspecialchars($row['phn']); ?>"
                                    data-role="<?php echo htmlspecialchars($row['role']); ?>">
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['phn']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td>
                                        <?php if (strtolower($row['role']) == 'employee') { ?>
                                            <a style="text-decoration:none;" href="#" class="edit-btn" onclick="openEditModal(<?php echo $row['user_id']; ?>)">Edit</a>
                                            <button class="delete-btn" data-id="<?php echo $row['user_id']; ?>">Delete</button>
                                        <?php } if (strtolower($row['role']) == 'admin') { ?>
                                            <!-- <button class="delete-btn" data-id="<?php echo $row['user_id']; ?>">Remove User</button> -->
                                        <?php } if (strtolower($row['role']) == 'customer') { ?>
                                            <button class="delete-btn" data-id="<?php echo $row['user_id']; ?>">Remove User</button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button class="new-user-btn btn btn-primary" data-id="">Add New User</button>

                    <!-- Modal -->
                    <div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="form-group"><input type="text" name="user_name" class="form-control" placeholder="Username (without spaces)" required></div>
                                        <div class="form-group"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
                                        <div class="form-group"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                                        <div class="form-group"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                                        <div class="form-group"><input type="text" name="phn" class="form-control" placeholder="Phone Number" required></div>
                                        <div class="form-group">
                                            <select name="role" class="form-control" required>
                                                <option value="" disabled selected>Select Role</option>
                                                <option value="customer">customer</option>
                                                <option value="admin">admin</option>
                                                <option value="employee">employee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-actions d-block justify-content-center gap-10">
                                        <button type="submit" name="add_user" class="btn btn-success px-4">Save</button>
                                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                        
                <div class="profile-container" style="width:90%;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Unique Code</th>
                                <th>Name</th>
                                <th>Price Range</th>
                                <th>Products</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <?php
                                // Fetch categories
                                while ($row = $result5->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['price_range']) . "</td>";
                                    echo "<td>";

                                    // Fetch products related to the current category
                                    $cat_code = $row['code'];
                                    $productQuery = "SELECT name FROM products WHERE cat_code = ?";
                                    $stmt = $conn->prepare($productQuery);
                                    $stmt->bind_param("s", $cat_code);
                                    $stmt->execute();
                                    $productResult = $stmt->get_result();

                                    // Display products in a scrollable dropdown
                                    echo '<select style="width: 100%; max-height: 40px; overflow-y: auto; border: none; border-radius: 8px; padding: 8px; background: #ffffff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); cursor: pointer;">';
                                    if ($productResult->num_rows > 0) {
                                        while ($productRow = $productResult->fetch_assoc()) {
                                            echo '<option style="padding: 8px;">' . htmlspecialchars($productRow['name']) . '</option>';
                                        }
                                    } else {
                                        echo '<option>No Products</option>';
                                    }
                                    echo '</select>';

                                    echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                
            <?php
                } // End admin check
            ?>
            <!-- Edit employee Modal HTML -->
            <div id="editUserModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Edit User</h2>
                    
                    <form id="editUserForm" onsubmit="event.preventDefault(); updateUser();">
                        <input type="hidden" id="user_id" name="user_id" value="">

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="user_name">Username</label>
                            <input type="text" id="user_name" name="user_name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" id="role" name="role" required>
                        </div>

                        <div class="form-group">
                            <label for="phn">Phone Number</label>
                            <input type="text" id="phn" name="phn" required>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                            <button type="submit" class="btn-secondary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php
                // Check if the logged-in user is an admin
                if(isset($_SESSION['role']) && $_SESSION['role'] == 'customer') {
            ?>
                <div class="profile-container" style="width:90%; display:block; text-align:center;">
                    <table>
                        <thead>
                            <th colspan="4"> My ORDERS </th>
                            <tr>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Order Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <?php while ($row = $result6->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td>Order Placed</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php
                } // End customer check
            ?>

            <br>

            <!-- Profile Section -->
            <div class="profile-container">
                <div class="profile-details">
                    <h1>My Profile</h1>
                    <h3>Username : <?php echo $name; ?> </h3>
                    <h3>Unique ID : <?php echo $user_id; ?> </h3>

                    <!-- Change Password Section -->
                    <div class="change-password">
                        <h3>Change Password</h3>
                        <form method="POST">
                            <input type="password" name="new_password" placeholder="New Password" required>
                            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                            <br><br>
                            <button type="submit">Update Password</button>
                        </form>
                    </div>
                </div>

                <div class="profile-picture" style="margin-top:34%;">
                    <!-- <img src="images/profile.png" alt="Profile Picture"> -->
                    <form action="logout.php" method="POST">
                            <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
            
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
    $(document).ready(function(){
        $('.new-user-btn').click(function(){
            $('#newUserModal').modal('show');
        });
    });
    // JavaScript for handling Filter, Edit and Delete operations
        document.addEventListener('DOMContentLoaded', function() {
            // Filter users by role
            document.querySelectorAll('.dropdown-content a').forEach(function(filterLink) {
                filterLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    const role = filterLink.getAttribute('data-role');
                    filterUsers(role);
                });
            });

            function filterUsers(role) {
                document.querySelectorAll('#userTable tr').forEach(function(row) {
                    if (role === 'all' || row.getAttribute('data-role') === role) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');

                    if (confirm("Are you sure you want to delete this user?")) {
                        // AJAX request to delete the user
                        fetch('delete_user.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    user_id: userId
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('user-' + userId).remove(); // Remove the row from the table
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                });
            });
        });

        function openEditModal(userId) {
    const userRow = document.getElementById(`user-${userId}`);
    
    // Fetch user details from the row's data attributes
    const name = userRow.getAttribute('data-name');
    const username = userRow.getAttribute('data-username');
    const email = userRow.getAttribute('data-email');
    const phn = userRow.getAttribute('data-phn');
    const role = userRow.getAttribute('data-role');
    
    // Populate modal fields with user data
    document.getElementById('user_id').value = userId; // Hidden field for user ID
    document.getElementById('name').value = name;
    document.getElementById('user_name').value = username;
    document.getElementById('email').value = email;
    document.getElementById('phn').value = phn;
    document.getElementById('role').value = role.toLowerCase(); // Ensure role is lowercase

    // Show the modal
    document.getElementById('editUserModal').style.display = 'flex'; // Adjust according to your modal's display method
}

function closeModal() {
    document.getElementById('editUserModal').style.display = 'none'; // Adjust according to your modal's display method
}

function updateUser() {
    const formData = $('#editUserForm').serialize(); // Get form data

    $.ajax({
        url: 'update_employee.php', // Endpoint to update user details
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response === 'success') {
                location.reload(); // Reload the page to see changes
            } else {
                alert('Error updating user: ' + response); // Handle error
            }
        },
        error: function(xhr, status, error) {
            console.error("Error updating user:", error);
        }
    });
}

</script>