<?php
session_start();
require '../includes/db.php';
require '../includes/mailer.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../login-forms/choose-to-login.html");
    exit();
}

$account_type = $_POST['account_type'];
$email = $_POST['email'];

if (empty($email)) {
    $_SESSION['error'] = "Email is required.";
    header("Location: ../login-forms/user-forgot-password.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: ../login-forms/user-forgot-password.php");
    exit();
}

// Set table and email field based on account type
if ($account_type === 'user') {
    $table = 'customers';
    $emailField = 'customer_emailAddress';
} elseif ($account_type === 'owner') {
    $table = 'owners';
    $emailField = 'owner_emailAddress';
} else {
    $_SESSION['error'] = "Invalid account type.";
    header("Location: ../login-forms/user-forgot-password.php");
    exit();
}

// Check if email exists
$stmt = $pdo->prepare("SELECT * FROM $table WHERE $emailField = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Email not found.";
    header("Location: ../login-forms/user-forgot-password.php");
    exit();
}

// Generate code and save
$code = rand(100000, 999999);
$update = $pdo->prepare("UPDATE $table SET reset_code = ? WHERE $emailField = ?");
$update->execute([$code, $email]);

$_SESSION['verification_code'] = $code;
$_SESSION['email_address'] = $email;
$_SESSION['account_type'] = $account_type;

// Send code
if (sendMail($email, $code)) {
    $_SESSION['success'] = "Verification code sent.";
    header("Location: ../login-forms/user-verify.php");
} else {
    $_SESSION['error'] = "Failed to send email.";
    header("Location: ../login-forms/user-forgot-password.php");
}
?>
