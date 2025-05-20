<?php
session_start();
require '../includes/db.php'; // your PDO connection

$customer_id = $_SESSION['customer_id'] ?? null;

try {
    $stmt = $pdo->prepare("
        SELECT b.branch_id, b.branch_name, b.branch_image, 
               COUNT(c.car_id) AS car_count
        FROM branches b
        INNER JOIN owners o ON b.owner_id = o.owner_id
        LEFT JOIN cars c ON b.branch_id = c.branch_id
        WHERE o.approval_status = 'approved'
        GROUP BY b.branch_id
    ");
    $stmt->execute();
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching branches: " . $e->getMessage();
}



$rsql = "
SELECT * FROM rentals
WHERE customer_id = ? 
AND rentals.reviewed = 0
AND bookingStatus_id = 5"; // 5 for completed status

$stmt = $pdo->prepare($rsql);
$stmt->execute([$customer_id]);
$pending_booking = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Page</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="side-nav-bar-style/style-user.css">
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
  
    <a href="user-dashboard.php">
      <img src="./image/nav-bar-icon/dashboard-icon.png" alt="Dashboard Icon">
      <span>Dashboard</span>
    </a>
    <a href="">
      <img src="./image/nav-bar-icon/car-list-icon.png" alt="Car Rental Shop Icon">
      <span>Car Rental Shop</span>
    </a>
    <a href="booking.php">
      <img src="./image/nav-bar-icon/booking-icon.png" alt="My Booking Icon">
      <span>My Booking</span>
    </a>
    <a href="#">
      <img src="./image/nav-bar-icon/settings-icon.png" alt="Settings Icon">
      <span>Settings</span>
    </a>
  </div>
  
  

  <!-- Header -->
  <div class="topbar">
    <div class="search-bar">
            <form method="GET" action="../user_process/car_list.php" class="search-bar d-flex align-items-center">
            <input type="text" name="search" placeholder="Search..." required>
            <button type="submit" style="border: none; background: transparent;">
              <img src="./image/nav-bar-icon/search-icon.png" alt="Search" />
            </button>
          </form>
          </div>
    <div class="d-flex gap-4">
          
            <a href="ratings.php" class="d-flex flex-column align-items-center position-relative" style="text-decoration: none; color: black;">
              <div class="position-relative">
                <img src="./image/nav-bar-icon/notif-icon.png" width="23" height="25" alt="Notification">
              
                <?php if ($pending_booking): ?>
                <!-- Show Red Badge if there is a Pending booking -->
                <span class="badge position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                  <span class="visually-hidden">New alerts</span>
                </span>
              <?php else: ?>
                <!-- No alert if no Pending booking -->
                <span class="badge position-absolute top-0 start-100 translate-middle p-1 bg-transparent border border-light rounded-circle"></span>
              <?php endif; ?>
              </div>
              <span style="font-size: 11px;">Notification</span>
            </a>
          
            <div class="account-info" id="accountInfo">
              <img src="./image/nav-bar-icon/account-icon.png" width="30" alt="Account">
              <span>Account</span>
            </div>
         </div>

    <!-- Profile and Logout Section -->
    <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
      <!-- Profile -->
      <a href="user-profile.php" class="text-decoration-none text-dark">
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
    <h4>Car Rental Shops</h4>

    <!-- lading-page-content" -->
    <div class="lading-page-content">

      <!-- card start -->
      <div class="d-flex flex-wrap justify-content-start">
      <?php foreach ($branches as $branch): ?>
        <div class="card text-center" style="width: 12rem; margin: 1rem 1rem 1rem 0;">
            <img src="../uploads/<?php echo htmlspecialchars($branch['branch_image']);?>" class="rounded-circle mx-auto d-block mt-3" alt="..." style="width: 130px; height: 130px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($branch['branch_name']);?></h5>
              <p class="card-text text-muted"><?php echo htmlspecialchars($branch['car_count']); ?></p>

              <a href="car-owner-shop-view.php?branch_id=<?php echo $branch['branch_id']; ?>" class="btn btn-dark w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-2" style="font-size: 13px;">
                <img src="./image/booking-icon/car-details-icon.png" alt="Rent Icon" style="width: 16px; height: 16px;">
                <span class="text-warning fw-semibold">View Cars</span>
              </a>
            </div>
        </div>

        <?php endforeach; ?>
          
          


      </div> <!-- card end-->
    
      </div><!-- lading-page-content-->
  </div>
  <script>
    const dropdownMenu = document.getElementById('dropdownMenu');
    const accountInfo = document.getElementById('accountInfo');

    // Toggle dropdown visibility on account info click
    accountInfo.addEventListener('click', function(event) {
      // Prevent click event from propagating to document
      event.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Close the dropdown when clicking outside of it
    document.addEventListener('click', function(event) {
      if (!accountInfo.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
      }
    });
  </script>
  <!-- Optional JS -->
  <script src="script.js"></script>
</body>
</html>

<style>
  /* Hover logout */
  .hover-effect {
    border-radius: 5px;
    transition: background-color 0.2s;
  }

  .hover-effect:hover {
    background-color: #fdc650;
  }
</style>