<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selfDrive = $_POST['selfDrive'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $fullname = $firstname . " " . $lastname;
    $driversAge = $_POST['driverAge'];
    $mobileNumber = $_POST['mobileNumber'];
    $licenseNumber = $_POST['licenseNumber'];
    $residency = $_POST['residency'];

    // Check if "I Confirm Driver's Age" checkbox is checked
    if (!isset($_POST['a']) || $_POST['a'] !== 'on') {
        $_SESSION['error'] = "You must confirm the driver's age is above 20 years old.";
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect back to this page
        exit();
    }

    // Validate input fields are not empty
    if (empty($selfDrive) || empty($firstname) || empty($lastname) || 
        empty($driversAge) || empty($mobileNumber) || empty($licenseNumber) || 
        empty($residency)) {
        $_SESSION['error'] = "You must fill all fields.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if ((int)$driversAge < 20) {
    $_SESSION['error'] = "Driver must be at least 20 years old.";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
    }  


    // Handle file uploads (license and residency images)
    if (isset($_FILES['licenseImage']) && $_FILES['licenseImage']['error'] == 0) {
        $licenseImage = $_FILES['licenseImage'];
        $licenseImagePath = '../uploads/' . basename($licenseImage['name']);
        move_uploaded_file($licenseImage['tmp_name'], $licenseImagePath);
    } else {
        $_SESSION['error'] = "Please upload a valid License Image.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_FILES['residencyImage']) && $_FILES['residencyImage']['error'] == 0) {
        $residencyImage = $_FILES['residencyImage'];
        $residencyImagePath = '../uploads/' . basename($residencyImage['name']);
        move_uploaded_file($residencyImage['tmp_name'], $residencyImagePath);
    } else {
        $_SESSION['error'] = "Please upload a valid Residency Image.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    // Store combined driver data in session
        $_SESSION['self_drive_data'] = [
            'driverType' => 'Self-Drive',
            'fullname' => $fullname,
            'driverAge' => $driversAge,
            'mobileNumber' => $mobileNumber,
            'licenseNumber' => $licenseNumber,
            'residency' => $residency,
            'licenseImage' => $licenseImagePath,
            'residencyImage' => $residencyImagePath
        ];


    // Redirect to the next page
    header('Location: ../user_landing/booking-third-process.php');
    exit();
}

?>
