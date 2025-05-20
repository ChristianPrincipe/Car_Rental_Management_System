<?php
session_start();
require '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../Login-forms/choose-to-create.html");
    exit;
}

$account_type = $_POST['account_type'] ?? '';

if ($account_type === 'user') {
    // USER SIGNUP VALIDATION
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $fullname = $firstname . ' ' . $lastname;
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['re_password'];

    // Validate fields
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../Login-forms/user-create-account.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../Login-forms/user-create-account.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../Login-forms/user-create-account.php");
        exit;
    }
    
    // Password validation: at least 8 chars and at least one number
    if (!preg_match('/^(?=.*\d).{8,}$/', $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and contain at least one number.";
        header("Location: ../Login-forms/user-create-account.php");
        exit;
    }

    // Check duplicate email
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_emailAddress = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already registered.";
        header("Location: ../Login-forms/user-create-account.php");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO customers (customer_name, customer_username, customer_emailAddress, customer_password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fullname, $username, $email, $hashedPassword]);

    //user sessions
    $_SESSION['customer_id'] = $pdo->lastInsertId();
    $_SESSION['customer_email'] = $email;
    $_SESSION['customer_name'] = $fullname;
    $_SESSION['account_type'] = 'user';

    $_SESSION['success'] = "User registered successfully!";
    header("Location: ../Login-forms/user-login.php");
    exit;

} elseif ($account_type === 'owner') {
    // OWNER SIGNUP VALIDATION
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $fullname = $firstname . ' ' . $lastname;
    $username = $_POST['username']; // fixed here
    $branchname = $_POST['branchname'];
    $email = $_POST['email'];
    $business_permit = $_POST['business_permit'] ?? '';
    $password = $_POST['password'];
    $confirm_password = $_POST['re_password'];

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($business_permit) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields including business permit are required.";
        header("Location: ../Login-forms/owner-create-account.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../Login-forms/owner-create-account.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../Login-forms/owner-create-account.php");
        exit;
    }
    
    // Password validation: at least 8 chars and at least one number
    if (!preg_match('/^(?=.*\d).{8,}$/', $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and contain at least one number.";
        header("Location: ../Login-forms/owner-create-account.php");
        exit;
    }

    // Check duplicate email
    $stmt = $pdo->prepare("SELECT * FROM owners WHERE owner_emailAddress = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already registered.";
        header("Location: ../Login-forms/owner-create-account.php");
        exit;
    }

    // Insert the owner into the owners table with approval status set to 'pending'
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO owners (owner_name, owner_username, owner_emailAddress, owner_password, owner_businessPermit, approval_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $username, $email, $hashedPassword, $business_permit, 'pending']);

    // Get the latest owner_id
    $owner_id = $pdo->lastInsertId();

    // Insert the new branch into the branches table
    $stmt = $pdo->prepare("INSERT INTO branches (branch_name, owner_id) VALUES (?, ?)");
    $stmt->execute([$branchname, $owner_id]);

    $branchId = $pdo->lastInsertId();

    // Owner session setup
    $_SESSION['branch_id'] = $branchId;
    $_SESSION['owner_id'] = $owner_id;  // Store the owner_id in session
    $_SESSION['owner_email'] = $email;
    $_SESSION['owner_name'] = $fullname;
    $_SESSION['account_type'] = 'owner';

    $_SESSION['success'] = "Owner registered successfully! Your account is pending approval.";
    header("Location: ../Login-forms/owner-login.php");
    exit;

} else {
    $_SESSION['error'] = "Invalid account type.";
    header("Location: ../Login-forms/choose-to-create.html");
    exit;
}
?>
