<?php
require '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ruleName = $_POST['rule_name'] ?? '';
    $branchId = $_POST['branch_id'] ?? '';

    if (!empty($ruleName) && !empty($branchId)) {
        $stmt = $pdo->prepare("INSERT INTO rules (rule_name, branch_id) VALUES (?, ?)");
        if ($stmt->execute([$ruleName, $branchId])) {
            echo "success";
        } else {
            echo "error inserting rule";
        }
    } else {
        echo "Missing rule name or branch ID";
    }
}
?>
