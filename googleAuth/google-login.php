<?php
require_once '../vendor/autoload.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT']);
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['account_type'])) {
    $_SESSION['account_type'] = $_GET['account_type'];  // Save the selected role
}

header('Location: ' . $client->createAuthUrl());
exit();

?>
