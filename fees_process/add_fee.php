<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fee_name = trim($_POST['fee_name']);
    $fee_amount = trim($_POST['fee_amount']);
    $branch_id = intval($_POST['branch_id']);

    // Validation
    if (empty($fee_name) || empty($fee_amount) || empty($branch_id)) {
        http_response_code(400); // Bad Request
        echo "Fee name, amount, and branch ID are required.";
        exit();
    }

    if (!is_numeric($fee_amount) || $fee_amount < 0) {
        http_response_code(400);
        echo "Invalid fee amount.";
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO fees (fee_name, fee_amount, branch_id) VALUES (:fee_name, :fee_amount, :branch_id)");
        $stmt->execute([
            ':fee_name' => $fee_name,
            ':fee_amount' => $fee_amount,
            ':branch_id' => $branch_id
        ]);

        // Redirect to owner-profile.php after success
        header("Location: ../onwer-landing/fees.php");
        exit;
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}
?>
