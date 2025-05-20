<?php
session_start();
require '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM admins WHERE admin_username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['admin_password'])) {
        // Set session variable
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['admin_username'];

        header('Location: ../admin_landing/admin-dashboard.php');
        exit();
    } else {
        // Optionally pass an error message
        header('Location: admin-login.php?error=invalid_credentials');
        exit();
    }
}
?>
