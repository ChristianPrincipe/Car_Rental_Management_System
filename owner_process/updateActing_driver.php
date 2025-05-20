<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['drivers_id'];
    $names = $_POST['name'];
    $prices = $_POST['price'];

    foreach ($ids as $index => $id) {
        $name = $names[$index];
        $price = $prices[$index];

        // Handle image upload if a new one was submitted
        if (!empty($_FILES['profile_image']['name'][$index])) {
            $imgName = $_FILES['profile_image']['name'][$index];
            $imgTmp = $_FILES['profile_image']['tmp_name'][$index];
            $uploadPath = '../uploads/' . basename($imgName);
            move_uploaded_file($imgTmp, $uploadPath);

            // Update with new image
            $sql = "UPDATE drivers SET driver_name = ?, drivers_price = ?, drivers_picture = ? WHERE drivers_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $imgName, $id]);
        } else {
            // Update without changing image
            $sql = "UPDATE drivers SET driver_name = ?, drivers_price = ? WHERE drivers_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $id]);
        }
    }

    header("Location: ../onwer-landing/owner-driver.php");
    exit;
}
