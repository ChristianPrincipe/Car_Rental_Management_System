<?php
include '../includes/db.php'; // Database connection
session_start();

$ownerId = $_SESSION['owner_id']; // Ensure owner is logged in

// Fetch the branch ID of the logged-in owner
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

// Retrieve form data
$carId = $_POST['car_id'];
$carModel = $_POST['car_name'];
$carTypeName = $_POST['car_type'];
$transmissionTypeName = $_POST['car_transmission'];
$fuelTypeName = $_POST['car_gas'];
$ac = $_POST['car_AC'];
$capacity = $_POST['car_seats'];
$price = $_POST['car_price'];
$description = $_POST['car_description'];



// Helper function to get or insert values in dropdown-related tables
function getOrInsert($pdo, $table, $column, $value) {
    $stmt = $pdo->prepare("SELECT {$table}_id FROM $table WHERE $column = ?");
    $stmt->execute([$value]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $row["{$table}_id"];
    }

    $insert = $pdo->prepare("INSERT INTO $table ($column) VALUES (?)");
    $insert->execute([$value]);
    return $pdo->lastInsertId();
}

// Get or insert type IDs
$carTypeId = getOrInsert($pdo, 'carType', 'carType_name', $carTypeName);
$transmissionTypeId = getOrInsert($pdo, 'transmissionType', 'transmissionType_name', $transmissionTypeName);
$fuelTypeId = getOrInsert($pdo, 'fuelType', 'fuelType_name', $fuelTypeName);

// Handle image uploads
function uploadImage($fileInputName) {
    $targetDir = "../uploads/";
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
        $filename = basename($_FILES[$fileInputName]["name"]);
        $targetFile = $targetDir . $filename;

        // Optional: Image type validation
        // $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        // if (!in_array($_FILES[$fileInputName]["type"], $allowedTypes)) return null;

        if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFile)) {
            return $filename;
        }
    }
    return null;
}

// Upload images
$carImages = [];
$carImages[] = uploadImage("carMain_image");
$carImages[] = uploadImage("carSide1_image");
$carImages[] = uploadImage("carSide2_image");
$carImages[] = uploadImage("carBack_image");

// Update car info
try {
    // Update car details in cars table
    $sql = "UPDATE cars 
            SET car_description = ?, carType_id = ?, car_model = ?, transmissionType_id = ?, AC = ?, capacity = ?, fuelType_id = ?, price = ? 
            WHERE car_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$description, $carTypeId, $carModel, $transmissionTypeId, $ac, $capacity, $fuelTypeId, $price, $carId]);

    // Remove old images
    $deleteImagesStmt = $pdo->prepare("DELETE FROM car_images WHERE car_id = ?");
    $deleteImagesStmt->execute([$carId]);

    // Insert new images if uploaded
    $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, carImages) VALUES (?, ?)");
    foreach ($carImages as $img) {
        if ($img !== null) {
            $imgStmt->execute([$carId, $img]);
        }
    }

    $_SESSION['message'] = "Car updated successfully!";
    header('Location: ../onwer-landing/car-listing.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: ../onwer-landing/car-listing.php');
    exit();
}
?>
