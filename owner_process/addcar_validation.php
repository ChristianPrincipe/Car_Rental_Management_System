<?php
include '../includes/db.php'; // Your DB connection

session_start(); // Ensure session is started
$ownerId = $_SESSION['owner_id']; // Must be set during login

$stmt = $pdo->prepare("SELECT branch_id FROM branches WHERE owner_id = ?");
$stmt->execute([$ownerId]);
$branchRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($branchRow) {
    $branchId = $branchRow['branch_id'];
} else {
    $_SESSION['error'] = "Branch not found for this owner.";
    header('Location: ../onwer-landing/car-listing.php');
    exit();
}

$carModel = $_POST['car_name'];
$carTypeName = $_POST['car_type'];
$transmissionTypeName = $_POST['car_transmission'];
$fuelTypeName = $_POST['car_gas'];
$ac = $_POST['car_AC']; // 'Yes' or 'No'
$capacity = $_POST['car_seats'];
$price = $_POST['car_price'];
$description = $_POST['car_description'];

// Helper to get or insert and return ID
function getOrInsert($pdo, $table, $column, $value) {
    // Check if the value already exists
    $stmt = $pdo->prepare("SELECT {$table}_id FROM $table WHERE $column = ?");
    $stmt->execute([$value]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        return $row["{$table}_id"]; // Return existing ID
    }
    
    // If the value doesn't exist, insert it
    $insert = $pdo->prepare("INSERT INTO $table ($column) VALUES (?)");
    $insert->execute([$value]);
    return $pdo->lastInsertId(); // Return the new ID
}

// Get or insert into respective tables
$carTypeId = getOrInsert($pdo, 'carType', 'carType_name', $carTypeName);
$transmissionTypeId = getOrInsert($pdo, 'transmissionType', 'transmissionType_name', $transmissionTypeName);
$fuelTypeId = getOrInsert($pdo, 'fuelType', 'fuelType_name', $fuelTypeName);  // Added fuel type insertion

// Upload images
function uploadImage($fileInputName) {
    $targetDir = "../uploads/";
    $filename = basename($_FILES[$fileInputName]["name"]);
    $targetFile = $targetDir . $filename;
    move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFile);
    return $filename;
}

$carImages = [
    uploadImage("carMain_image"),
    uploadImage("carSide1_image"),
    uploadImage("carSide2_image"),
    uploadImage("carBack_image")
];

// Insert into `cars` table
try {
    $sql = "INSERT INTO cars (branch_id, car_description, carType_id, car_model, transmissionType_id, AC, capacity, fuelType_id, price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$branchId, $description, $carTypeId, $carModel, $transmissionTypeId, $ac, $capacity, $fuelTypeId, $price]);
    
    // Get the car_id for the newly inserted car
    $carId = $pdo->lastInsertId();
    
    // Insert all images into `car_images`
    $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, carImages) VALUES (?, ?)");
    foreach ($carImages as $img) {
        $imgStmt->execute([$carId, $img]);
    }

    // Success message in session
    session_start();
    $_SESSION['message'] = "Car added successfully!";
    header('Location: ../onwer-landing/car-listing.php');
    exit();
} catch (PDOException $e) {
    // Failure message in session
    session_start();
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: ../onwer-landing/car-listing.php');
    exit();
}
?>
