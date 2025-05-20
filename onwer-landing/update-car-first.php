<?php
include '../includes/db.php'; // Your DB connection

session_start(); // Ensure session is started
$ownerId = $_SESSION['owner_id']; // Ensure owner_id is set during login

// Fetch the branch ID of the logged-in owner
$stmt = $pdo->prepare("SELECT branch_id FROM branches WHERE owner_id = ?");
$stmt->execute([$ownerId]);
$branchRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($branchRow) {
    $branchId = $branchRow['branch_id'];
} else {
    $_SESSION['error'] = "Branch not found for this owner.";
    header('Location: ../onwer-landing/car-listing.php');
    exit();
}

// Fetch cars for the branch (where ownerId matches and the branchId is found)
$stmt = $pdo->prepare("
  SELECT c.car_id, c.car_model, c.car_description, c.price,
         ci.carImages,
         ct.carType_name, t.transmissionType_name, f.fuelType_name,
         c.AC, c.capacity
  FROM cars c
  JOIN carType ct ON c.carType_id = ct.carType_id
  JOIN transmissionType t ON c.transmissionType_id = t.transmissionType_id
  JOIN fuelType f ON c.fuelType_id = f.fuelType_id
  LEFT JOIN car_images ci ON c.car_id = ci.car_id
  WHERE c.branch_id = ?
  GROUP BY c.car_id
");

$stmt->execute([$branchId]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Page</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="side-nav-bar-style-owner/style.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
  
  <a href="owner-dashboard.php">
    <img src="./image/nav-bar-icon/dashboard-icon.png" alt="Dashboard Icon">
    <span>Dashboard</span>
  </a>
  <a href="car-listing.php">
    <img src="./image/nav-bar-icon/car-list-icon.png" alt="Car Rental Shop Icon">
    <span>Car Listing</span>
  </a>
  <a href="booked-cars.php">
    <img src="./image/nav-bar-icon/booking-icon.png" alt="My Booking Icon">
    <span>Booked Cars</span>
  </a>
  <a href="#">
    <img src="./image/nav-bar-icon/settings-icon.png" alt="Settings Icon">
    <span>Settings</span>
  </a>
</div>

<!-- Header -->
<div class="topbar d-flex justify-content-end">
  <div class="account-info" id="accountInfo">
    <img src="./image/nav-bar-icon/account-icon.png" width="30" alt="Account">
    <span>Account</span>
  </div>

  <!-- Profile and Logout Section -->
  <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" 
  style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; 
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
  
    <!-- Profile -->
    <a href="owner-profile.php" class="text-decoration-none text-dark">
      <div class="d-flex align-items-center mb-2 px-2 py-2 m-2 hover-effect">
        <img src="./image/nav-bar-icon/profile-icon.png" alt="Profile" width="20" height="20" class="me-2">
        <span>Profile</span>
      </div>
    </a>

    <!-- Logout -->
    <a href="../index.php" class="text-decoration-none text-dark">
      <div class="d-flex align-items-center px-2 py-2 m-2 hover-effect">
        <img src="./image/nav-bar-icon/logout-icon.png" alt="Logout" width="20" height="20" class="me-2">
        <span>Logout</span>
      </div>
    </a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  <h4>Car Listing</h4>

  <!-- Car Listing -->
  <div class="lading-page-content">
    <a href="car-listing.php">
      <button style="all: unset; cursor: pointer;">
        <img src="./image/back-button.png" alt="Back" style="width: 20px; height: 20px; margin-bottom: 20px;">
      </button>
    </a>
<div class="d-flex flex-wrap gap-3">
    <?php if ($cars): ?>
      <?php foreach ($cars as $car): ?>
        <div class="card" style="width: 200px; border: 1px solid #ddd; border-radius: 10px;">
          <div class="card-body p-3 pt-2">
              
              <!-- Image (Responsive) -->
            <div class="mb-2">
              <img src="../uploads/<?= $car['carImages']; ?>" alt="<?= $car['car_model']; ?>" class="img-fluid rounded" />
            </div>

            <!-- Car Name -->
            <h4 class="card-title fw-bold mb-2" style="font-size: 16px;"><?= $car['car_model']; ?></h4>
        
            <!-- Vehicle Type -->
            <div class="mb-2" style="font-size: 13px; color: #6c757d;"><?= $car['carType_name']; ?></div>  

            <!-- Price -->
            <div class="mb-2" style="font-size: 13px;">
              <span>₱ <?= number_format($car['price'], 2); ?>/Day</span>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <span class="fw-semibold" style="font-size: 12px;">Status:</span>
              <span class="badge bg-success text-white" style="font-size: 11px;">Available</span>
            </div>

            <!-- Update Car Button -->
            <a href="update-car-second.php?car_id=<?= $car['car_id']; ?>"
               class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1"
               style="font-size: 12px; width: 100%;">
              <img src="./image/car-details.png" alt="Rent Icon" 
                   style="width: 12px; height: 12px;">
              <span class="text-warning fw-semibold">Update Car</span>
            </a>
        
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No cars available.</p>
    <?php endif; ?>

  </div>
  <!-- lading-page-content end -->

</div>
<!-- Main Content end -->

<script>
  const dropdownMenu = document.getElementById('dropdownMenu');
  const accountInfo = document.getElementById('accountInfo');

  // Toggle dropdown visibility on account info click
  accountInfo.addEventListener('click', function(event) {
    // Prevent click event from propagating to document
    event.stopPropagation();
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
  });
</script>

<!-- Optional JS -->
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
  .carousel-item img {
    filter: brightness(1);
    opacity: 1;
    background-color: #817979;
  }

  .drop-buttons{
    top: 60px;
    z-index: 102;
  }

  .dropdown-container{
    background-color: rgb(255, 255, 255);
    width:auto;
    border: solid 1px #f0a500;
    padding: 5px;
    border-radius: 30px;
  }
  /* Hover logout */
  .hover-effect {
    border-radius: 5px;
    transition: background-color 0.2s;
  }

  .hover-effect:hover {
    background-color: #fdc650;
  }
</style>
