<?php

    session_start();

    require '../includes/db.php';

    if(!isset($_SESSION['customer_id'])){
        header('Location: ../Login-forms/user-login.php');
        exit();
    }

    $customer_id = $_SESSION['customer_id'];

    // If the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the data from the form
        $firstname = $_POST['name'];
        $lastname = $_POST['lastname'];
        $fullname = $firstname . ' ' . $lastname;
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];
        $email = $_POST['email'];


        if (isset($_FILES['profile-image']) && $_FILES['profile-image']['error'] == 0) {
            $imgName = uniqid() . '_' . basename($_FILES['profile-image']['name']);
            $imgTmp = $_FILES['profile-image']['tmp_name'];
            $uploadPath = '../uploads/' . $imgName;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            
            // Check if the file type is valid
            if (in_array($_FILES['profile-image']['type'], $allowedTypes)) {
                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    // Update the image path in the database
                    $stmt = $pdo->prepare("UPDATE customers SET customer_image = ? WHERE customer_id = ?");
                    $stmt->execute([$imgName, $_SESSION['customer_id']]);
                } else {
                    echo "Failed to upload image.";
                }
            } else {
                echo "Invalid file type. Only JPG, JPEG, PNG files are allowed.";
            }
        }
        
        

        // Prepare the UPDATE query
        $stmt = $pdo->prepare("UPDATE customers SET 
        customer_name = ?, 
        customer_age = ?, 
        customer_gender = ?, 
        customer_birthdate = ?, 
        customer_address = ?, 
        customer_emailAddress = ?
        WHERE customer_id = ?");

   $stmt->execute([$fullname, $age, $gender, $birthdate, $address, $email, $customer_id
]);


    // Redirect after successful update
    header('Location: ../user_landing/user-profile.php');
    exit();

    }

    // Fetch the current user data
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$_SESSION['customer_id']]);
    $user = $stmt->fetch();
?>