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
                        <p  class="side-p">Please log in to manage your bookings, explore our cars, or continue where you left off.</p>
                    </div>
 
                    <div class="choose-main-form">
                    

                        <form action="../validations/login_validate.php" method="POST">
                            <input type="hidden" name="account_type" value="user">
                            <h4 class="choose-login-h4">Login User Account</h4>

                            <?php
                            if(isset($_SESSION['success'])){
                                echo '<div class="alert alert-success text-center">' . $_SESSION['success'] . '</div>';
                                unset($_SESSION['success']);
                            }
                            
                            if(isset($_SESSION['error'])){
                                echo '<div class="alert alert-danger text-center">' . $_SESSION['error'] . '</div>';
                                unset($_SESSION['error']);
                            }
                            ?>

                            <div class="input-wrapper">
                                <img src="../login-icon/email.png" class="input-icon">
                                <input type="text" name="email_or_username" placeholder="Email or Username" required>
                            </div>
                              
                            <div class="input-wrapper">
                                <img src="../login-icon/lock.png" class="input-icon">
                                <input type="password" name="password" placeholder="Password" required>
                              </div>

                              
                              
                            <a class="forgot-password-design" href="user-forgot-password.php">Forgot Password?</a>

                            <div class="mt-3 text-center">
                                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>
                              </div>
                            <button type ="submit" class="single-button">Login</button>
                            <h6 style="align-self: center; margin: 0; margin-bottom: 5px;">or</h6>

                            
                                <button class="google-login-button">
                                    <a href="../googleAuth/google-login.php?account_type=user" style="text-decoration: none; color:white">
                                        <img src="../Simg/google-icon.png" alt=""> Login with Google
                                    </a>
                                </button>
                            
                            
                            <span>Don't have an account?<a href="user-create-account.php">Create account</a></span>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script src="script.js"></script>
</body>
</html>
