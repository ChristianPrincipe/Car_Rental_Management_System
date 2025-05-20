<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rule_id = $_POST['rule_id'];
    $rulesInput = $_POST['rulesInput'];

    // Get branch_id from owner_id in session
    $ownerId = $_SESSION['owner_id'];

    $stmt = $pdo->prepare("SELECT branch_id FROM branches WHERE owner_id = ?");
    $stmt->execute([$ownerId]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($branch) {
        $branch_id = $branch['branch_id'];
    } else {
        $_SESSION['error'] = "Branch not found.";
        header("Location: ../onwer-landing/view-rules.php");
        exit();
    }

    // Update rule in the database
    $sql = "UPDATE rules SET rule_name = :rule_name 
            WHERE rule_id = :rule_id AND branch_id = :branch_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rule_name', $rulesInput);
    $stmt->bindParam(':rule_id', $rule_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->execute();

    // Redirect after update
    $_SESSION['message'] = "Rule updated successfully.";
    header("Location: ../onwer-landing/view-rules.php");
    exit();
}
?>
