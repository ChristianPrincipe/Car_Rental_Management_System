<?php
session_start();
require '../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Ensure request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../Login-forms/choose-to-create.html");
    exit();
}

$account_type = $_POST['account_type'];
$login_input = $_POST['email_or_username'];
$password = $_POST['password'];

$recaptchaSecret = $_ENV['RECAPTCHA_SECRET_KEY'];
$recaptchaResponse = $_POST['g-recaptcha-response'];

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
$captchaSuccess = json_decode($verify);

if (!$captchaSuccess->success) {
    $_SESSION['error'] = "Captcha verification failed. Please try again!";
    $redirect = ($account_type === 'user') ? 'user-login.php' : 'owner-login.php';
    header("Location: ../login-forms/$redirect");
    exit();
}

// Empty field check
if (empty($login_input) || empty($password)) {
    $_SESSION['error'] = "All fields are required.";
    $redirect = ($account_type === 'user') ? 'user-login.php' : 'owner-login.php';
    header("Location: ../login-forms/$redirect");
    exit();
}

// Password validation: at least 8 characters and at least one number
if (!preg_match('/^(?=.*\d).{8,}$/', $password)) {
    $_SESSION['error'] = "Password must be at least 8 characters long and contain at least one number.";
    $redirect = ($account_type === 'user') ? 'user-login.php' : 'owner-login.php';
    header("Location: ../login-forms/$redirect");
    exit();
}

// User login
if ($account_type === 'user') {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_emailAddress = ? OR customer_username = ?");
    $stmt->execute([$login_input, $login_input]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['customer_password'])) {
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['customer_email'] = $user['customer_emailAddress'];
        $_SESSION['customer_name'] = $user['customer_name'];
        $_SESSION['role'] = 'user';
        $_SESSION['success'] = "User login successful.";
        header("Location: ../user_landing/user-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username/email or password.";
        header("Location: ../login-forms/user-login.php");
        exit();
    }
}

// Owner login
if ($account_type === 'owner') {
    $stmt = $pdo->prepare("SELECT * FROM owners WHERE owner_emailAddress = ? OR owner_username = ?");
    $stmt->execute([$login_input, $login_input]);
    $owner = $stmt->fetch();

    if ($owner && password_verify($password, $owner['owner_password'])) {
        if ($owner['approval_status'] === 'rejected') {
            $_SESSION['error'] = "Your request has been rejected. You cannot proceed.";
            header("Location: ../login-forms/owner-login.php");
            exit();
        } elseif ($owner['approval_status'] === 'pending') {
            $_SESSION['error'] = "Your account is pending approval.";
            header("Location: ../login-forms/owner-login.php");
            exit();
        }

        $_SESSION['branch_id'] = $owner['branch_id'];
        $_SESSION['owner_id'] = $owner['owner_id'];
        $_SESSION['owner_email'] = $owner['owner_emailAddress'];
        $_SESSION['owner_name'] = $owner['owner_name'];
        $_SESSION['role'] = 'owner';
        $_SESSION['success'] = "Owner login successful.";
        header("Location: ../onwer-landing/owner-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username/email or password.";
        header("Location: ../login-forms/owner-login.php");
        exit();
    }
}

?>
