<?php
require_once '../vendor/autoload.php';
require '../includes/db.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT']);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $email = $userInfo->email;
        $name = $userInfo->name;
        $picture = $userInfo->picture;

        $role = $_SESSION['account_type'] ?? 'user'; // Default to user

        if ($role === 'user') {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_emailAddress = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                // Insert new user
                $insert = $pdo->prepare("INSERT INTO customers (customer_name, customer_emailAddress, customer_image) VALUES (?, ?, ?)");
                $insert->execute([$name, $email, $picture]);
                $user_id = $pdo->lastInsertId();
            } else {
                $user_id = $user['customer_id'];
                $name = $user['customer_name'];
                $picture = $user['customer_image'];
            }

            // Set session
            $_SESSION['user_type'] = 'google';
            $_SESSION['account_type'] = 'user';
            $_SESSION['customer_name'] = $name;
            $_SESSION['customer_email'] = $email;
            $_SESSION['customer_image'] = $picture;
            $_SESSION['customer_id'] = $user_id;

            $_SESSION['success'] = "Logged in with Google!";
            header('Location: ../user_landing/user-dashboard.php');
            exit();

         } 
        //  elseif ($role === 'owner') {
        //     // Check if owner exists
        //     $stmt = $pdo->prepare("SELECT * FROM owners WHERE owner_emailAddress = ?");
        //     $stmt->execute([$email]);
        //     $owner = $stmt->fetch();

        //     if ($owner) {
        //         // Check approval status
        //         if ($owner['approval_status'] !== 'approved') {
        //             $_SESSION['error'] = "Your account is pending approval.";
        //             header('Location: ../Login-forms/owner-login.php');
        //             exit();
        //         }

        //         // Owner exists and is approved
        //         $owner_id = $owner['owner_id'];
        //         $name = $owner['owner_name'];
        //         $picture = $owner['owner_image'];
        //     } else {
        //         // Insert new owner
        //         $insert = $pdo->prepare("INSERT INTO owners (owner_name, owner_emailAddress, owner_image) VALUES (?, ?, ?)");
        //         $insert->execute([$name, $email, $picture]);
        //         $owner_id = $pdo->lastInsertId();
        //     }

        //     // Set session
        //     $_SESSION['user_type'] = 'google';
        //     $_SESSION['account_type'] = 'owner';
        //     $_SESSION['owner_name'] = $name;
        //     $_SESSION['owner_email'] = $email;
        //     $_SESSION['owner_image'] = $picture;
        //     $_SESSION['owner_id'] = $owner_id;

        //     $_SESSION['success'] = "Logged in with Google as Owner!";
        //     header('Location: ../onwer-landing/owner-dashboard.php');
        //     exit();
        // }

    // } else {
    //     $_SESSION['error'] = "Google login failed.";
    //     header('Location: ../Login-forms/owner-login.php');
    //     exit();
    }
} else {
    $_SESSION['error'] = "Google login failed.";
    header('Location: ../Login-forms/user-login.php');
    exit();
}
?>
