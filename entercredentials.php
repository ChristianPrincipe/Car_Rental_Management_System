<?php

require 'includes/db.php'; // This should set up $pdo

try {
    $admin_username = "jimmy";
    $admin_password = password_hash("jimmy123", PASSWORD_DEFAULT); // hash the password

    $sql = "INSERT INTO admins (admin_username, admin_password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':username' => $admin_username,
        ':password' => $admin_password
    ]);

    echo "New admin inserted successfully.";
} catch (PDOException $e) {
    echo "Error inserting admin: " . $e->getMessage();
}
?>