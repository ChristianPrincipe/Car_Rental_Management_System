<?php
include '../includes/db.php'; // Adjust path

// Ensure the admin is logged in (optional but recommended)
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-forms/admin-login.php");  // Redirect to login page if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $owner_id = intval($_POST['owner_id']); // Ensure owner_id is an integer
    $action = $_POST['action'];

    // Validate the action value
    if ($action !== 'approve' && $action !== 'reject') {
        // Handle invalid action
        echo "Invalid action!";
        exit();
    }

    // Determine approval or rejection status
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    try {
    // Prepare the SQL statement to update approval status and set admin_id
    $stmt = $pdo->prepare("UPDATE owners SET approval_status = :status, admin_id = :admin_id WHERE owner_id = :owner_id");

    // Bind parameters
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
    $admin_id = $_SESSION['admin_id'];
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);

    // Execute
    $stmt->execute();

    // Redirect
    header("Location: admin-dashboard.php");
    exit();
} catch (PDOException $e) {
    echo "Error executing query: " . $e->getMessage();
    exit();
}
}
?>
