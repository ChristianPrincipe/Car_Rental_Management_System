<?php
require '../includes/db.php';

if (isset($_GET['id'])) {
    $driverId = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM drivers WHERE drivers_id = ?");
    $stmt->execute([$driverId]);

    header("Location: ../onwer-landing/owner-driver.php"); // Redirect back to the driver list
    exit();
} else {
    echo "Invalid request.";
}
?>
