<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the_final_db";

// the_final_db

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!"; // optional for debugging
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
