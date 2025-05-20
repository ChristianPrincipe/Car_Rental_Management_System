<?php
session_start();
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
                        <form action="../forgot_process/verify_code.php" method="POST">
                            <h4 class="choose-login-h4-password">Forgot Password</h4>
                            <input type="hidden" name="account_type" value="owner">
                            
                            <?php
                            if(isset($_SESSION['success'])){
                                echo "<div class='alert alert-success text-center'>" . $_SESSION['success'] . "</div>";
                                unset($_SESSION['success']);
                            }
                            
                            if(isset($_SESSION['error'])){
                                echo "<div class='alert alert-danger text-center'>" . $_SESSION['error'] . "</div>";
                                unset($_SESSION['error']);
                            }
                            ?>

                            <input class="long-box-input" type="number" placeholder="Enter Code">
                            <a class="create-account" href="owner-new-password.php">Verify</a>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <script src="script.js"></script>
</body>
</html>
