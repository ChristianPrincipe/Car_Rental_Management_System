<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../' );
$dotenv->load();

$siteKey = $_ENV['RECAPTCHA_SITE_KEY'];



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/style.css">

</head>
<body>
        <div class="main d-flex justify-content-center align-items-center">

            <div class="form-container d-flex flex-column">

                <!-- close button -->
                <div class="position-relative">
                    <button class="button-design back-button "><a href="choose-to-login.html"><img src="../button/back-button.png"></a></button>
                    <button class="button-design close-button"><a href="../index.php"><img src="../button/xButton.png"></a></button>
                </div>
                
                <div class="design">
                    <div class="side-infromation">
                        <img src="../lading-page-image/logo.png">
                        <h3>Welcome Back to JJC Car Rental</h3>
                    </div>
 
                    <div class="choose-main-form">
                    

                        <form action="admin_validate.php" method="POST">
                            <input type="hidden" name="account_type" value="user">
                            <h4 class="choose-login-h4">Login Admin Account</h4>

                           

                            <div class="input-wrapper">
                                <img src="../login-icon/email.png" class="input-icon">
                                <input type="text" name="username" placeholder="User Name" required>
                            </div>
                              
                            <div class="input-wrapper">
                                <img src="../login-icon/lock.png" class="input-icon">
                                <input type="password" name="password" placeholder="Password" required>
                              </div>

                            <button type ="submit" class="single-button">Login</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <script src="script.js"></script>
</body>
</html>
