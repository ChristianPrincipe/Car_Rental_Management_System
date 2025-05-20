<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="main d-flex justify-content-center align-items-center">
        <div class="form-container d-flex flex-column">

            <!-- Close buttons -->
            <div class="position-relative">
                <button class="button-design back-button">
                    <a href="choose-to-login.html"><img src="../button/back-button.png" alt="Back"></a>
                </button>
                <button class="button-design close-button">
                    <a href="../index.php"><img src="../button/xButton.png" alt="Close"></a>
                </button>
            </div>

            <div class="design">
                <div class="side-infromation">
                    <img src="../lading-page-image/logo.png" alt="Logo">
                    <h3>Welcome Back to JJC Car Rental</h3>
                    <p class="side-p">
                        Please log in to manage your bookings, explore our cars, or continue where you left off.
                    </p>
                </div>

                <div class="choose-main-form">
                    <form action="../forgot_process/new_password.php" method="POST">
                        <input type="hidden" name="account_type" value="user">
                        <h4 class="choose-login-h4-password">Reset Password</h4>
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
                        <h6 class="input-h6">Enter New Password</h6>
                        <input class="long-box-input" type="password" name="password" placeholder="Enter New Password" required>

                        <h6 class="input-h6">Re-enter New Password</h6>
                        <input class="long-box-input" type="password" name="re_password" placeholder="Re-enter Password" required>

                        <button type="submit" class="create-account">Confirm</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
