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
        <!-- <div class="login-container"> -->
            <div class="login-left">
                <h2>SIGN UP</h2>
                <form action="new_user.php" method="POST">
                    <div class="input-box">
                        <i class="fas fa-user-shield"></i>
                        <input type="text" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-user-shield"></i>
                        <input type="text" name="username" placeholder="Enter your Username" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phn" placeholder="Enter your phone number" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="new_password" placeholder="Set Password" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <select name="role">
                            <option selected disabled> How would you describe yourself ?</option>
                            <option> Customer </option>
                            <option> Employee </option>
                        </select>
                    </div>
                    <button type="submit" class="login-btn">Sign Up</button>
                </form>
            </div>
            <!-- <div class="login-right">
                <img src="images/login.jpg" alt="Medical Illustration" />
            </div> -->
        <!-- </div> -->
    </body>
</html>