<?php
    require 'db_conn.php';

    header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
    header("Pragma: no-cache"); // HTTP 1.0
    header("Expires: 0"); // Proxies

    // Check if the form to change password is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password == $confirm_password) {
            $update_sql = "UPDATE users SET password = ? WHERE user_name = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, 'ss', $confirm_password, $username);
            mysqli_stmt_execute($update_stmt);

            header("Refresh: 2; url=loginpage.php");
            echo "<script>alert('Password changed successfully!');</script>";
        } 
        else {
            header("Refresh: 2; url=forgot_passwd.php");
            echo "<script>alert('Passwords do not match!');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MediSphere - Login</title>
        <link rel="stylesheet" href="css\login.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="login-container">
            <div class="login-left">
                <h2>Change Your Password</h2>
                <form method="POST">
                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="new_password" placeholder="New Password" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="login-btn">Change Password</button>
                </form>
            </div>
            <div class="login-right">
                <img src="images/login.jpg" alt="Medical Illustration" />
            </div>
        </div>
    </body>
</html>