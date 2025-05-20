<?php
session_start();
require '../includes/db.php';

// Check if the form is being submitted via POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../login-forms/user-verify.php");
    exit();
}

// Get form data
$newPassword = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['re_password'] ?? '');
$email = $_SESSION['email_address'] ?? '';
$account_type = $_SESSION['account_type'] ?? '';

// Validate input
if (empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['error'] = "Please enter both password and confirmation.";
    header("Location: ../login-forms/user-new-password.php");
    exit();
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: ../login-forms/user-new-password.php");
    exit();
}

// Validate password strength (optional)
if (strlen($newPassword) < 8) {
    $_SESSION['error'] = "Password must be at least 8 characters long.";
    header("Location: ../login-forms/user-new-password.php");
    exit();
}

// Determine table and email field based on account type
if ($account_type === 'user') {
    $table = 'customers';
    $emailField = 'customer_emailAddress';
    $passwordField = 'customer_password';
} elseif ($account_type === 'owner') {
    $table = 'owners';
    $emailField = 'owner_emailAddress';
    $passwordField = 'owner_password';
} else {
    $_SESSION['error'] = "Invalid account type.";
    header("Location: ../login-forms/choose-to-login.html");
    exit();
}

// Hash the new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update the password in the database
$stmt = $pdo->prepare("UPDATE $table SET $passwordField = ? WHERE $emailField = ?");
$stmt->execute([$hashedPassword, $email]);

// Clear session data
unset($_SESSION['email_address']);
unset($_SESSION['account_type']);
unset($_SESSION['verification_code']);

// Success
$_SESSION['success'] = "Your password has been successfully updated!";
if ($account_type === 'user') {
    header("Location: ../login-forms/user-login.php");
} else {
    header("Location: ../login-forms/owner-login.php");
}
exit();
?>
