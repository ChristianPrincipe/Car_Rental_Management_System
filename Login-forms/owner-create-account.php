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
                        <p  class="side-p">Please log in to manage your listed vehicles, respond to booking requests, or track your earnings. Let’s keep your car working for you!</p>
                    </div>

                    <div class="choose-main-form">
                        <form action="../validations/signup_validate.php" method="POST">
                            <input type="hidden" name="account_type" value="owner">
                            <h4>Create owner account</h4>

                            <?php

                            if(isset($_SESSION['error'])){
                                echo "<div class='alert alert-danger text-center'>" . $_SESSION['error'] . "</div>";
                                unset($_SESSION['error']);
                            }

                            ?>
                            
                            <div class="firstname-lastname">
                                <div  class="name-input-design">
                                    <h6 class="input-h6-align">Enter First Name</h6>
                                    <input class="double-line-input" type="text" name="firstname" placeholder="First name" required>
                                </div>

                                <div  class="name-input-design">
                                    <h6 class="input-h6-align">Enter Last Name</h6>
                                    <input  class="double-line-input"  type="text" name="lastname" placeholder="Last name" required>
                                </div>
                            </div>
                            <h6 class="input-h6">Enter Username</h6>
                            <input class="long-box-input" type="text" name="username" placeholder="User Name" required>
                            <h6 class="input-h6">Enter Branch Name</h6>
                            <input class="long-box-input" type="text" name="branchname" placeholder="Branch Name" required>
                            <h6 class="input-h6">Enter email</h6>
                            <input class="long-box-input" type="email" name="email" placeholder="Email" required>
                            <h6 class="input-h6">Enter business permit</h6>
                            <input class="long-box-input" type="number" name="business_permit" placeholder="Enter number" required>

                            <div class="password-re-enter">
                                <div  class="name-input-design">
                                    <h6 class="input-h6-align">Enter password</h6>
                                    <input class="double-line-input" type="password" name="password" placeholder="Password" required>
                                </div>

                                <div  class="name-input-design">
                                    <h6 class="input-h6-align">Re-enter password</h6>
                                    <input class="double-line-input" type="password" name="re_password" placeholder="Re-enter password" required>
                                </div>
                            </div>

                            
                            <button type = "submit" class="create-account">Create account</button>
                           <span>Already have an account? <a href="owner-login.php">Login</a></span>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <script src="script.js"></script>
</body>
</html>
f