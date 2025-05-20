<?php
session_start();
require '../includes/db.php';

// Ensure owner is logged in
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../login-forms/owner-login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];

// Fetch branch data using owner_id (include branch_id)
$psql = "SELECT branch_id, branch_image, branch_name, branch_address, branch_number 
         FROM branches WHERE owner_id = ?";
$stmt = $pdo->prepare($psql);

try {
    $stmt->execute([$owner_id]);
    $bname = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($bname) {
        $branch_id = $bname['branch_id'];
        $branch_name = $bname['branch_name'] ?? 'Default Branch Name';
        $branch_image = $bname['branch_image'] ?? 'default-image.jpg';
        $branch_address = $bname['branch_address'] ?? 'Default Address';
        $branch_number = $bname['branch_number'] ?? '000-000-0000';
    } else {
        die("No branch found for the owner.");
    }
} catch (PDOException $e) {
    die("Error fetching branch data: " . $e->getMessage());
}

// Sanitize and fetch form inputs
$branch_name = trim($_POST['name'] ?? '');
$branch_address = trim($_POST['location'] ?? '');
$branch_number = trim($_POST['number'] ?? '');

// Handle file upload if image is provided
$updateImage = false;
if (isset($_FILES['profile-image']) && $_FILES['profile-image']['error'] == UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['profile-image']['tmp_name'];
    $fileName = basename($_FILES['profile-image']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        $newFileName = uniqid('branch_', true) . '.' . $fileExt;
        $uploadPath = '../uploads/' . $newFileName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $updateImage = true;
        }
    }
}

// Update the branch info
try {
    if ($updateImage) {
        $sql = "UPDATE branches 
                SET branch_name = ?, branch_address = ?, branch_number = ?, branch_image = ?
                WHERE branch_id = ? AND owner_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$branch_name, $branch_address, $branch_number, $newFileName, $branch_id, $owner_id]);
    } else {
        $sql = "UPDATE branches 
                SET branch_name = ?, branch_address = ?, branch_number = ?
                WHERE branch_id = ? AND owner_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$branch_name, $branch_address, $branch_number, $branch_id, $owner_id]);
    }

    $_SESSION['success'] = "Branch profile updated successfully!";
    header("Location: ../onwer-landing/owner-profile.php");
    exit();
} catch (PDOException $e) {
    die("Error updating profile: " . $e->getMessage());
}
?>
