<?php
require '../includes/db.php';
session_start();

// Check if the customer is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "Unauthorized access.";
    exit;
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Loop through each rental review submitted
    foreach ($_POST['review'] as $rental_id) {
        $rating = $_POST['rating_' . $rental_id] ?? null;
        $review_text = $_POST['comment'][$rental_id] ?? null;
        $review_date = date("Y-m-d H:i:s");

        // Basic validation
        if (empty($rating) || empty($review_text)) {
            echo "Please fill up all fields for rental ID " . htmlspecialchars($rental_id) . ".";
            continue; // Skip this rental if not fully filled
        }

        // Insert into the review table
        $stmt = $pdo->prepare("INSERT INTO review (rental_id, customer_id, rating, review_text, review_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$rental_id, $customer_id, $rating, $review_text, $review_date]);

        // Update the rentals table to set the 'reviewed' field to 1
        $updateStmt = $pdo->prepare("UPDATE rentals SET reviewed = 1 WHERE rental_id = ?");
        $updateStmt->execute([$rental_id]);
    }

    // Redirect to the user dashboard after the review process
    header('Location: ../user_landing/user-dashboard.php');
}
?>
