<?php
session_start();
require '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../login-forms/user-verify.php");
    exit();
}

$code = trim($_POST['code'] ?? '');
$email = $_SESSION['email_address'] ?? '';
$account_type = $_SESSION['account_type'] ?? '';

if (empty($email) || empty($code) || empty($account_type)) {
    $_SESSION['error'] = "Missing information.";
    header("Location: ../login-forms/user-verify.php");
    exit();
}

// Determine table and email field
if ($account_type === 'user') {
    $table = 'customers';
    $emailField = 'customer_emailAddress';
    $codeField = 'reset_code';
} elseif ($account_type === 'owner') {
    $table = 'owners';
    $emailField = 'owner_emailAddress';
    $codeField = 'reset_code';
} else {
    $_SESSION['error'] = "Invalid account type.";
    header("Location: ../login-forms/choose-to-login.html");
    exit();
}

// Check code in database
$stmt = $pdo->prepare("SELECT * FROM $table WHERE $emailField = ? AND $codeField = ?");
$stmt->execute([$email, $code]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Invalid email or verification code.";
    header("Location: ../login-forms/user-verify.php");
    exit();
}

// Success
$_SESSION['success'] = "Verification successful. Please reset your password.";
if ($account_type === 'user') {
    header("Location: ../login-forms/user-new-password.php");
} else {
    header("Location: ../login-forms/owner-new-password.php");
}
exit();

?>
