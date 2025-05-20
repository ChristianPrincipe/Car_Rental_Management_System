<?php
require '../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rental_id = $_POST['rental_id'];

    if (!empty($rental_id)) {
        $stmt = $pdo->prepare("UPDATE rentals SET bookingStatus_id = 3 WHERE rental_id = ?");
        if ($stmt->execute([$rental_id])) {
            // Redirect or success message
            header("Location: ../user_landing/booking.php");
            exit();
        } else {
            echo "Failed to cancel booking.";
        }
    } else {
        echo "Invalid booking ID.";
    }
} else {
    echo "Invalid request.";
}
