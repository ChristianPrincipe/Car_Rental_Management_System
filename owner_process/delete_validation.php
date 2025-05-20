<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['owner_id'])) {
    http_response_code(401);
    echo "Unauthorized access.";
    exit;
}

$ownerId = $_SESSION['owner_id'];


if (isset($_POST['car_id']) && !empty($_POST['car_id'])) {
    $carId = $_POST['car_id'];

    try {
        // Fetch branch of logged-in owner
        $stmt = $pdo->prepare("SELECT branch_id FROM branches WHERE owner_id = ?");
        $stmt->execute([$ownerId]);
        $branchRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$branchRow) {
            http_response_code(404);
            echo "Branch not found for this owner.";
            exit;
        }

        $branchId = $branchRow['branch_id'];

        // Check if the car belongs to this owner's branch
        $stmt = $pdo->prepare("SELECT car_id FROM cars WHERE branch_id = ? AND car_id = ?");
        $stmt->execute([$branchId, $carId]);
        $carRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$carRow) {
            http_response_code(403);
            echo "Car not found or you're not authorized to delete this car.";
            exit;
        }

        // Check if the car has active bookings
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE car_id = ?");
        $stmt->execute([$carId]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409); // Conflict
            echo "Cannot delete this car because it has active bookings.";
            exit;
        }

        // Begin transaction
        $pdo->beginTransaction();


        // Delete associated images
        $imgStmt = $pdo->prepare("SELECT carImages FROM car_images WHERE car_id = ?");
        $imgStmt->execute([$carId]);
        $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $image) {
            $imagePath = "../uploads/" . $image['carImages'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete records
        // Start by deleting related images first
        $stmt = $pdo->prepare("DELETE FROM car_images WHERE car_id = ?");
        $stmt->execute([$carId]);

        // Then delete the car
        $stmt = $pdo->prepare("DELETE FROM cars WHERE car_id = ?");
        $stmt->execute([$carId]);


        $pdo->commit();

        http_response_code(200);
        echo "Car deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo "Error deleting car: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Car ID is missing!";
}
?>
