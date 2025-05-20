<?php
session_start();
require '../includes/db.php'; // Make sure $pdo is your PDO connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ownerId = isset($_POST['owner_id']) ? intval($_POST['owner_id']) : 0;
    $selectedDriverId = $_POST['selected_driver_id'] ?? null;

    if (!$selectedDriverId) {
        exit("No driver selected.");
    }

    if ($ownerId === 0) {
        $_SESSION['error'] = "No owner ID provided.";
        header('Location: ../user_landing/ActingDriver-booking-second-process.php');
        exit();
    }

    // Fetch the selected acting driver for this owner
    $query = "SELECT d.drivers_id, d.driver_name, d.drivers_price, d.drivers_picture
              FROM drivers d
              JOIN driverstype dt ON d.drivertype_id = dt.driversType_id
              WHERE d.owner_id = ? AND dt.driversType_name = 'Acting Driver' AND d.drivers_id = ?
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$ownerId, $selectedDriverId]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($driver) {
        // Count completed rentals for that driver
        $queryRides = "SELECT COUNT(*) as ridesCompleted
                       FROM rentals
                       WHERE drivers_id = ? AND bookingStatus_id = 5";

        $stmtRides = $pdo->prepare($queryRides);
        $stmtRides->execute([$driver['drivers_id']]);
        $ridesCount = $stmtRides->fetch(PDO::FETCH_ASSOC);
        $ridesCompleted = $ridesCount['ridesCompleted'] ?? 0;

        // Update total price in session, Only add acting driver price if it hasn't been added yet
        if (!isset($_SESSION['driver_fee_added']) || $_SESSION['acting_drive_data']['driverID'] !== $driver['drivers_id']) {
            $totalPrice = $_SESSION['booking_data']['totalPrice'] ?? 0;
            $totalPrice += $driver['drivers_price'];
            $_SESSION['booking_data']['totalPrice'] = $totalPrice;
            $_SESSION['driver_fee_added'] = true;
        }

        // Store acting driver info including rides completed in session
        $_SESSION['acting_drive_data'] = [
            'actingDriver' => true,
            'driverID' => $driver['drivers_id'],
            'driverName' => $driver['driver_name'],
            'driversPrice' => $driver['drivers_price'],
            'driversPicture' => $driver['drivers_picture'],
            'ridesCompleted' => $ridesCompleted
        ];

        header('Location: ../user_landing/booking-third-process.php');
        exit();
    } else {
        $_SESSION['error'] = "No acting driver found matching your selection.";
        header('Location: ../user_landing/ActingDriver-booking-second-process.php');
        exit();
    }
}
?>
