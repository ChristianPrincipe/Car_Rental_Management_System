<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fee_id'])) {
    $fee_id = $_POST['fee_id'];

    // Check if the fee belongs to the logged-in owner's branch (for security)
    $owner_id = $_SESSION['owner_id'];
    $stmt = $pdo->prepare("SELECT * FROM fees f JOIN branches b ON f.branch_id = b.branch_id WHERE f.fee_id = ? AND b.owner_id = ?");
    $stmt->execute([$fee_id, $owner_id]);
    $fee = $stmt->fetch();

    if ($fee) {
        // Fee belongs to this owner, so delete it
        $stmt = $pdo->prepare("DELETE FROM fees WHERE fee_id = ?");
        $stmt->execute([$fee_id]);
        header("Location: ../onwer-landing/fees.php"); // Or wherever the fee list is
        exit;
    } else {
        // Fee not found or doesn't belong to owner
        echo "Unauthorized or fee not found.";
    }
} else {
    echo "Invalid request.";
}
