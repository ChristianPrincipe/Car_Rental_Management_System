<?php
session_start();
require '../includes/db.php';

if (isset($_POST['rule_id'])) {
    $ruleId = $_POST['rule_id'];

    $stmt = $pdo->prepare("DELETE FROM rules WHERE rule_id = ?");
    if ($stmt->execute([$ruleId])) {
        $_SESSION['success'] = "Rule deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete rule.";
    }
}

header("Location: ../onwer-landing/view-rules.php");
exit();