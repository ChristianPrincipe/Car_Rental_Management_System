<?php
include '../includes/db.php'; // Your DB connection

session_start(); // Start the session to access session variables

// Check if the owner and customer IDs are available in the session
if (!isset($_SESSION['owner_id'])) {
    echo "Owner ID is missing from session.";
    exit();
}

// Get and sanitize the form data
$driverName = htmlspecialchars(trim($_POST['driver_name']));
$price = htmlspecialchars(trim($_POST['price']));
$driverType = htmlspecialchars(trim($_POST['driver_type'])); // Example: "acting driver" or "self drive"

// Assuming the owner and customer IDs are from the session or other source
$ownerId = $_SESSION['owner_id']; // Make sure these values are set

// Check if the profile image is uploaded and there are no errors
if (isset($_FILES['profile-image']) && $_FILES['profile-image']['error'] == 0) {
    $imgName = uniqid() . '_' . basename($_FILES['profile-image']['name']);
    $imgTmp = $_FILES['profile-image']['tmp_name'];
    $uploadDir = '../uploads/';
    $uploadPath = $uploadDir . $imgName;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    // Check if the file type is valid
    if (in_array($_FILES['profile-image']['type'], $allowedTypes)) {
        // Ensure upload directory exists, if not, create it
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory with proper permissions
        }

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($imgTmp, $uploadPath)) {
            // Insert the driver type if it doesn't already exist
            $stmt = $pdo->prepare("SELECT driversType_id FROM driverstype WHERE driversType_name = ?");
            $stmt->execute([$driverType]);
            $driverTypeId = $stmt->fetchColumn();

            if (!$driverTypeId) {
                // Insert new driver type if not found
                $stmt = $pdo->prepare("INSERT INTO driverstype (driversType_name) VALUES (?)");
                $stmt->execute([$driverType]);
                $driverTypeId = $pdo->lastInsertId(); // Get the inserted driver type ID
            }

            // Insert the driver into the drivers table
            $stmt = $pdo->prepare("INSERT INTO drivers (owner_id, driver_name, drivers_price, drivers_picture, drivertype_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ownerId, $driverName, $price, $imgName, $driverTypeId]);

            // Success message and redirect
            $_SESSION['message'] = "Driver added successfully!";
            header('Location: ../onwer-landing/owner-profile.php');
            exit();
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "Invalid image file type. Only JPG, JPEG, PNG files are allowed.";
    }
} else {
    echo "No image file uploaded or there was an error with the file.";
}
?>
