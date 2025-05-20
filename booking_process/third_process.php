<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the details from the form
    $firstname = $_POST['details_fname'];
    $lastname = $_POST['details_lname'];
    $fullname = $firstname . ' ' . $lastname;  // Concatenate first and last name

    // Other fields
    $noPerson = $_POST['details_noPerson'];
    $phoneNumber = $_POST['details_phoneNumber'];
    $city = $_POST['details_city'];
    $barangay = $_POST['details_barangay'];
    $zipcode = $_POST['details_zipcode'];
    $emailAddress = $_POST['details_emailAddress'];
    $additionalInfo = $_POST['details_additionalInfo'];

    // You can now use these variables for further processing, like validation or storing them in the session.
    
    // Example of storing data in the session
    $_SESSION['user_details'] = [
        'fullname' => $fullname,
        'noPerson' => $noPerson,
        'phoneNumber' => $phoneNumber,
        'city' => $city,
        'barangay' => $barangay,
        'zipcode' => $zipcode,
        'emailAddress' => $emailAddress,
        'additionalInfo' => $additionalInfo
    ];

    // Redirect to another page or process further as needed
    header("Location: ../user_landing/booking-forth-process.php");
    exit();
}
?>
