<?php
session_start();
require '../includes/db.php';

$customerId = $_SESSION['customer_id'];

$booking_data = $_SESSION['booking_data'] ?? [];
$self_drive = $_SESSION['self_drive_data'] ?? [];
$acting_drive = $_SESSION['acting_drive_data'] ?? [];
$user_details = $_SESSION['user_details'] ?? [];

// 1. Insert rental type
// Check if rentalType already exists
$stmt = $pdo->prepare("SELECT rentalType_id FROM rentaltype WHERE rentalType_name = ?");
$stmt->execute([$booking_data['rentalType']]);
$existingType = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingType) {
    $rentalType_id = $existingType['rentalType_id'];
} else {
    // Insert if not found
    $stmt = $pdo->prepare("INSERT INTO rentaltype (rentalType_name) VALUES (?)");
    $stmt->execute([$booking_data['rentalType']]);
    $rentalType_id = $pdo->lastInsertId();
}


// 2. Insert locations
// Check if location already exists
$stmt = $pdo->prepare("SELECT location_id FROM locations WHERE location_delivery = ? AND location_return = ?");
$stmt->execute([$booking_data['deliveryLocation'], $booking_data['returnLocation']]);
$existingLocation = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingLocation) {
    $location_id = $existingLocation['location_id'];
} else {
    // Insert if not found
    $stmt = $pdo->prepare("INSERT INTO locations (location_delivery, location_return) VALUES (?, ?)");
    $stmt->execute([$booking_data['deliveryLocation'], $booking_data['returnLocation']]);
    $location_id = $pdo->lastInsertId();
}


// 3. Check if rental period already exists
$stmt = $pdo->prepare("SELECT rentalPeriod_id FROM rentalperiods 
                       WHERE start_date = ? AND return_date = ? 
                       AND start_time = ? AND return_time = ?");
$stmt->execute([
    $booking_data['startDate'],
    $booking_data['returnDate'],
    $booking_data['startTime'],
    $booking_data['returnTime']
]);

$existingPeriod = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingPeriod) {
    $rentalPeriod_id = $existingPeriod['rentalPeriod_id'];
} else {
    // Insert only if not existing
    $stmt = $pdo->prepare("INSERT INTO rentalperiods (start_date, return_date, start_time, return_time) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $booking_data['startDate'],
        $booking_data['returnDate'],
        $booking_data['startTime'],
        $booking_data['returnTime']
    ]);
    $rentalPeriod_id = $pdo->lastInsertId();
}





// 4. Insert proof of residency (for self-drive only)
$proof_id = null;
if (!empty($self_drive)) {
    $proofSQL = "INSERT INTO proofofresidency (proofOfResidency_image, proofOfResidency_name) VALUES (?, ?)";
    $stmt = $pdo->prepare($proofSQL);
    $stmt->execute([$self_drive['residencyImage'], $self_drive['residency']]);
    $proof_id = $pdo->lastInsertId();
}

// 5. Update customer data
$customerData = "UPDATE customers
                SET customer_contactNumber = ?, zip_code = ?
                WHERE customer_id = ?";
$stmt = $pdo->prepare($customerData);
$stmt->execute([$user_details['phoneNumber'], $user_details['zipcode'], $customerId]);


// 6. Check if driver type already exists
$dsql = "SELECT driverstype_id FROM driverstype WHERE driversType_name = ?";
$stmt = $pdo->prepare($dsql);
$stmt->execute([$self_drive['driverType']]);
$existingDriverType = $stmt->fetch(PDO::FETCH_ASSOC);

$drivertype_name = $self_drive['driverType'] ?? ($acting_drive['driverType'] ?? null);
$drivertype_id = null;

if ($drivertype_name) {
    // Check if driver type already exists
    $stmt = $pdo->prepare("SELECT driverstype_id FROM driverstype WHERE driversType_name = ?");
    $stmt->execute([$drivertype_name]);
    $existingDriverType = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingDriverType) {
        $drivertype_id = $existingDriverType['driverstype_id'];
    } else {
        // Insert only if not found and not a predefined type
        if (!$existingDriverType) {
            $stmt = $pdo->prepare("INSERT INTO driverstype (driversType_name) VALUES (?)");
            $stmt->execute([$drivertype_name]);
            $drivertype_id = $pdo->lastInsertId();
        }
    }
}




$driver_id = null;

if (!empty($self_drive) && $self_drive['driverType'] === 'Self-Drive') {
    // Insert the self-drive driver
    $stmt = $pdo->prepare("INSERT INTO drivers (
        customer_id, driver_name, drivers_age, driverslicense_number,
        driverlicense_image, drivers_contactNumber,
        proofOfResidency_id, drivertype_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $customerId,
        $self_drive['fullname'],
        $self_drive['driverAge'],
        $self_drive['licenseNumber'],
        $self_drive['licenseImage'],
        $self_drive['mobileNumber'],
        $proof_id,
        $drivertype_id
    ]);

    $driver_id = $pdo->lastInsertId();

} elseif (!empty($acting_drive) && $acting_drive['actingDriver'] === true) {
    // Acting driver: select existing one by ID
    $driver_id = $acting_drive['driverID'] ?? null;
}



// 8. Insert final rental record
$number_person = $user_details['noPerson'];
$bookingStatus_id = 1; // e.g., default "Pending" status

$insertRental = $pdo->prepare("INSERT INTO rentals (
    customer_id, car_id, drivers_id, rentalType_id,
    locations_id, rentalPeriod_id, estimated_total, booking_date,
    booking_time, bookingStatus_id, number_person
) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)");

$insertRental->execute([ 
    $customerId,
    $booking_data['carId'],
    $driver_id,
    $rentalType_id,
    $location_id,
    $rentalPeriod_id,
    $booking_data['totalPrice'],
    $bookingStatus_id,
    $number_person
]);

// 9. Update car status to 'Not Available'
$updateCarStatus = $pdo->prepare("UPDATE cars SET car_status = 'Not Available' WHERE car_id = ?");
$updateCarStatus->execute([$booking_data['carId']]);


// Clear session data to avoid reusing on next booking
unset($_SESSION['booking_data']);
unset($_SESSION['self_drive_data']);
unset($_SESSION['acting_drive_data']);
unset($_SESSION['user_details']);

// Redirect to success page
header("Location: ../user_landing/booking.php");
exit;
?>
