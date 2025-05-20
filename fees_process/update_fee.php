<?php
session_start();

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fee_id = intval($_POST['fee_id']); // Fee ID
    $fee_amount = trim($_POST['fee_amount']); // Fee amount

    // Validation
    if (empty($fee_amount) || empty($fee_id)) {
        http_response_code(400); // Bad Request
        echo "Fee amount and fee ID are required.";
        exit();
    }

    if (!is_numeric($fee_amount) || $fee_amount < 0) {
        http_response_code(400);
        echo "Invalid fee amount.";
        exit();
    }

    try {
        // Update only the fee amount
        $stmt = $pdo->prepare("UPDATE fees SET fee_amount = :fee_amount WHERE fee_id = :fee_id");
        $stmt->execute([
            ':fee_amount' => $fee_amount,
            ':fee_id' => $fee_id
        ]);

        // Check if any row was updated
        if ($stmt->rowCount() > 0) {
            echo "success";
        } else {
            http_response_code(404); // Not Found
            echo "Fee not found or no changes made.";
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}
?>
