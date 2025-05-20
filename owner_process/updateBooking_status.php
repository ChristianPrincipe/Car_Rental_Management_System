<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'], $_POST['rental_id'])) {
        $action = $_POST['action'];
        $rental_id = $_POST['rental_id'];

        // Initialize status
        $status = null;

        if ($action === 'approve') {
            $status = 'Approved';
        } elseif ($action === 'reject') {
            $status = 'Rejected';
        } elseif ($action === 'complete') {
            // Directly update to Completed (ID = 5)
            $stmt = $pdo->prepare("UPDATE rentals SET bookingStatus_id = 5 WHERE rental_id = ?");
            $stmt->execute([$rental_id]);

            if ($stmt->rowCount() > 0) {
                // Get the car_id associated with this rental
                $carStmt = $pdo->prepare("SELECT car_id FROM rentals WHERE rental_id = ?");
                $carStmt->execute([$rental_id]);
                $car = $carStmt->fetch();

                if ($car) {
                    // Update the car_status to 'Available'
                    $updateCar = $pdo->prepare("UPDATE cars SET car_status = 'Available' WHERE car_id = ?");
                    $updateCar->execute([$car['car_id']]);

                    echo "Booking marked as Completed and car set to Available.";
                } else {
                    echo "Car not found for this rental.";
                }
            } else {
                echo "Failed to update booking to Completed.";
            }
            exit;
        } else {
            echo "Invalid action.";
            exit;
        }

        // For approve or reject actions
        $sql = "UPDATE rentals SET bookingStatus_id = (SELECT bookingStatus_id FROM bookingstatus WHERE bookingStatus_name = ?) WHERE rental_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $rental_id]);

        if ($stmt->rowCount() > 0) {
            echo "Booking status updated to $status.";
        } else {
            echo "Failed to update booking status.";
        }
    } else {
        echo "Missing parameters.";
    }
} else {
    echo "Invalid request method.";
}
?>
