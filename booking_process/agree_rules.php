<?php
session_start();

// Check if "I Confirm Driver's Age" checkbox is checked
    if (!isset($_POST['agree']) || $_POST['agree'] !== 'on') {
        $_SESSION['error'] = "You must agree in the rules before proceding.";
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect back to this page
        exit();
    }

    // Redirect to the next page
    header('Location: ../user_landing/booking-fifth-process.php');
    exit();
?>