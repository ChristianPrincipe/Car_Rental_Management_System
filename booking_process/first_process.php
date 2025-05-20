<?php
session_start();
require '../includes/db.php'; // Adjust path to your PDO connection file

// var_dump($_POST['branch_id']);
// exit();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate essential fields
    $requiredFields = ['startDate', 'returnDate', 'startTime', 'returnTime'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $_SESSION['error'] = "Missing required field: $field";
            header('Location: ../user_landing/booking-first-process.php');
            exit();
        }
    }

    $rentalType = $_POST['rentalType'] ?? 'Self-Pickup';
    $deliveryLocation = '';
    $returnLocation = '';
    $deliveryFee = 0;

    $branchId = $_POST['branch_id'];
    $carId = $_POST['car_id'];

    // Fetch car's branch info (also secures branch_id)
    $stmt = $pdo->prepare("SELECT b.branch_id, b.branch_address FROM cars c 
                           JOIN branches b ON c.branch_id = b.branch_id 
                           WHERE c.car_id = ?");
    $stmt->execute([$carId]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) {
        $_SESSION['error'] = "Branch address not found for the selected car.";
        header('Location: ../user_landing/booking-first-process.php');
        exit();
    }

    // Handle delivery vs self-pickup
    if ($rentalType === "Delivery") {
        $deliveryLocation = $_POST['deliveryLocation'] ?? '';
        $returnLocation = $_POST['returnLocation'] ?? '';

        if (empty($deliveryLocation) || empty($returnLocation)) {
            $_SESSION['error'] = "Please provide both delivery and return locations.";
            header('Location: ../user_landing/booking-first-process.php');
            exit();
        }

        // Get delivery fee
        $stmt = $pdo->prepare("SELECT fee_amount FROM fees WHERE branch_id = ? AND fee_name = 'Delivery Fee'");
        $stmt->execute([$branchId]); // Use $branchId here instead of $carId
        $fee = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fee) {
            $deliveryFee = $fee['fee_amount'];
        } else {
            $_SESSION['error'] = "No delivery fee set for this car.";
            header('Location: ../user_landing/booking-first-process.php');
            exit();
        }

    } else {
        // Self-pickup: use branch address
        $deliveryLocation = $returnLocation = $branch['branch_address'];
    }

    // Get date and time
    $startDate = $_POST['startDate'];
    $returnDate = $_POST['returnDate'];
    $startTime = $_POST['startTime'];
    $returnTime = $_POST['returnTime'];

    // Combine and calculate duration
    $startDateTime = new DateTime($startDate . ' ' . $startTime);
    $returnDateTime = new DateTime($returnDate . ' ' . $returnTime);

    if ($returnDateTime <= $startDateTime) {
        $_SESSION['error'] = "Return date and time must be after the start.";
        header('Location: ../user_landing/booking-first-process.php');
        exit();
    }

    $interval = $startDateTime->diff($returnDateTime);
    $durationDays = $interval->days;
    $durationHours = $interval->h;

    // Fetch base price
    $stmt = $pdo->prepare("SELECT price FROM cars WHERE car_id = ?");
    $stmt->execute([$carId]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        $_SESSION['error'] = "Car price not found.";
        header('Location: ../user_landing/booking-first-process.php');
        exit();
    }

    $basePrice = $car['price'];

    // Charges
    $additionalChargePerDay = 100;
    $additionalChargePerHour = 20;

    $durationCharge = ($durationDays * $additionalChargePerDay) + ($durationHours * $additionalChargePerHour);
    $totalPrice = $basePrice + $durationCharge + $deliveryFee;

    // Store in session
    $_SESSION['booking_data'] = [
        'carId' => $carId,
        'branch_id' => $branchId,
        'rentalType' => $rentalType,
        'deliveryLocation' => $deliveryLocation,
        'returnLocation' => $returnLocation,
        'startDate' => $startDate,
        'returnDate' => $returnDate,
        'startTime' => $startTime,
        'returnTime' => $returnTime,
        'deliveryFee' => $deliveryFee,
        'totalPrice' => $totalPrice,
        'durationDays' => $durationDays,
        'durationHours' => $durationHours,
        // 'reference' => uniqid('BK-', true)
    ];

    // Redirect to next process
    header('Location: ../user_landing/seflDrive-booking-second-process.php');
    exit();
}
?>
