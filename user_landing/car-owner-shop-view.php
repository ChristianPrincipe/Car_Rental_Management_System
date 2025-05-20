<?php

session_start();

require '../includes/db.php';

$customer_id = $_SESSION['customer_id'] ?? null;

// Check if the branch_id is passed through URL or session
if (isset($_GET['branch_id'])) {
    $branch_id = $_GET['branch_id'];
} elseif (isset($_SESSION['branch_id'])) {
    $branch_id = $_SESSION['branch_id'];
} else {
    // Handle the error if branch_id is not set
    echo "Branch ID is missing.";
    exit;
}

// SQL Query: Get the first image for each car based on the uploaded_at timestamp
$sql = "SELECT 
            cars.*, 
            ci.carImages, 
            cartype.carType_name, 
            transmissionType.transmissionType_name, 
            fuelType.fuelType_name,
            branches.branch_image,
            branches.branch_name
        FROM branches
        LEFT JOIN cars ON branches.branch_id = cars.branch_id
        LEFT JOIN (
            SELECT car_id, carImages
            FROM (
                SELECT car_id, carImages, ROW_NUMBER() OVER (PARTITION BY car_id ORDER BY uploaded_at) AS rn
                FROM car_images
            ) AS ranked_images
            WHERE ranked_images.rn = 1
        ) ci ON cars.car_id = ci.car_id
        LEFT JOIN cartype ON cars.cartype_id = cartype.carType_id 
        LEFT JOIN transmissionType ON cars.transmissionType_id = transmissionType.transmissionType_id
        LEFT JOIN fuelType ON cars.fuelType_id = fuelType.fuelType_id
        WHERE branches.branch_id = ?
        ORDER BY cars.car_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$branch_id]);  // Use indexed array to bind the parameter
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rsql = "
SELECT * FROM rentals
WHERE customer_id = :customer_id 
AND rentals.reviewed = 0
AND bookingStatus_id = 5"; // 5 for Pending status

$stmt = $pdo->prepare($rsql);
$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$pending_booking = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch one booking record if available

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Page</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="./side-nav-bar-style/style-user.css">

<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
  
    <a href="user-dashboard.php">
      <img src="./image/nav-bar-icon/dashboard-icon.png" alt="Dashboard Icon">
      <span>Dashboard</span>
    </a>
    <a href="rental-shop.php">
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
    <h4><?php echo htmlspecialchars($cars[0]['branch_name'] ?? 'Branch Name'); ?></h4>

    <!-- lading-page-content" -->
    <div class="lading-page-content">

      <a href="rental-shop.php">
        <button style="all: unset; cursor: pointer;">
          <img src="./image/rental-car-icon/back-button.png" alt="Back" style="width: 20px; height: 20px;">
        </button>
      </a>

      <!-- card start -->
      <div class="d-flex flex-wrap justify-content-center">
        

      <?php if (empty($cars) || (count($cars) === 1 && !$cars[0]['car_id'])): ?>
    <p class="text-center text-muted mt-4">No car available yet.</p>
<?php else: ?>
        <!-- Start -->
<?php foreach($cars as $car):?>
  
 <?php if (!$car['car_id']) continue; ?> 
<?php
    $carId = $car['car_id'];
    $averageRating = 0;

    // Fetch rating
    $sql = "SELECT rating
            FROM review
            JOIN rentals ON review.rental_id = rentals.rental_id
            WHERE rentals.car_id = :car_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['car_id' => $carId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($reviews) > 0) {
        $totalRating = array_sum(array_column($reviews, 'rating'));
        $averageRating = round($totalRating / count($reviews), 1);
    }

    // Only fetch booking dates if car is Not Available
    $startDate = $returnDate = null;
    if ($car['car_status'] === 'Not Available') {
        $sql = "SELECT rentalperiods.start_date, rentalperiods.return_date
                FROM rentals
                JOIN rentalperiods ON rentals.rentalPeriod_id = rentalperiods.rentalPeriod_id
                WHERE rentals.car_id = :car_id
                ORDER BY rentalperiods.start_date DESC
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['car_id' => $carId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $startDate = $booking['start_date'];
            $returnDate = $booking['return_date'];
        }
    }
?>


        <div class="card m-2 shadow rounded-3 overflow-hidden" style="width: 260px;">
        
          <!-- Car image + Profile image -->
          <div class="position-relative p-3 d-flex justify-content-center">

            <!-- Clickable Car Image -->
              <img src="../uploads/<?php echo htmlspecialchars ($car['carImages']);?>" alt="Toyota Van" 
                  class="rounded" 
                  style="width: 230px; height: 140px;">
          
            <!-- Clickable Profile Image -->
             <a href="car-owner-shop-view.php?branch_id=<?php echo $car['branch_id'] ; ?>">
              <img src="../uploads/<?php echo htmlspecialchars ($car['branch_image']);?>" alt="User" 
                  class="rounded-circle border border-2 border-white position-absolute" 
                  style="width: 40px; height: 40px; bottom: -10px; right: 15px;">
            </a>
          </div>
          
        
          <!-- Card Body -->
          <div class="card-body p-3 pt-2">
            <h4 class="card-title fw-bold mb-2" style="font-size: 18px;"><?php echo htmlspecialchars ($car['car_model']);?></h4>
          
            <!-- Vehicle Type -->
            <div class="mb-2" style="font-size: 14px; color: #6c757d;"><?php echo htmlspecialchars ($car['carType_name']);?></div>
          
           <!-- Rating -->
               <?php if ($averageRating > 0): ?>
              <div class="d-flex align-items-center mb-2">
                <div class="me-2 text-warning" style="font-size: 16px;">
                  <?php
                    $filledStars = floor($averageRating);
                    $emptyStars = 5 - $filledStars;
                    echo str_repeat('★', $filledStars) . str_repeat('☆', $emptyStars);
                  ?>
                </div>
                <span class="badge bg-light text-dark border rounded-pill" style="font-size: 12px;">
                  <?php echo $averageRating; ?>/5
                </span>
              </div>
              <?php else: ?>
                <div class="d-flex align-items-center" style="gap: 4px;">
                  <div class="text-muted" style="font-size: 16px;">☆☆☆☆☆</div>
                    <span class="badge bg-light text-dark border rounded-pill" style="font-size: 12px;">0/5</span>
                </div>
              <?php endif; ?>

              <!-- Status -->
                        <div class="mb-3">
                            <span class="fw-semibold" style="font-size: 12px;">Status:</span>
                            <?php if ($car['car_status'] === 'Available'): ?>
                              <span class="badge bg-success text-white" style="font-size: 11px;">Available</span>
                          <?php else: ?>
                              <span class="badge bg-danger text-white" style="font-size: 11px;">Not Available</span><br>
                              <span class="" style="font-size: 11px;">
                                In: <?php echo htmlspecialchars($startDate); ?> to <?php echo htmlspecialchars($returnDate); ?></span>
                          <?php endif; ?>
                        </div>
          
            <!-- Seats & Transmission -->
            <div class="d-flex justify-content-between mb-3">
              <div class="d-flex align-items-center" style="font-size: 14px;">
                <img src="./image/rental-car-icon/seats-icon.png" alt="Seats" class="me-2" style="width: 20px; height: 20px;">
                <?php echo htmlspecialchars ($car['capacity']);?> Seats
              </div>
              <div class="d-flex align-items-center" style="font-size: 14px;">
                <img src="./image/rental-car-icon/transmission-icon.png" alt="Manual" class="me-2" style="width: 20px; height: 20px;">
                <?php echo htmlspecialchars ($car['transmissionType_name']);?>
              </div>
            </div>
          
            <!-- Fuel & Price -->
            <div class="d-flex justify-content-between mb-3">
              <div class="d-flex align-items-center" style="font-size: 14px;">
                <img src="./image/rental-car-icon/gas-icon.png" alt="Diesel" class="me-2" style="width: 20px; height: 20px;">
                <?php echo htmlspecialchars ($car['fuelType_name']);?>
              </div>
              <div class="d-flex align-items-center" style="font-size: 14px;">
                <img src="./image/rental-car-icon/peso-icon.png" alt="Peso" class="me-2" style="width: 18px; height: 17px;">
                <?php echo htmlspecialchars ($car['price']);?>
              </div>
            </div>

            
  
           <!-- Rent Now Button -->
           <a href="view-car-details.php?from=car-owner-shop-view&car_id=<?php echo $car['car_id']; ?>&branch_id=<?php echo $branch_id; ?>" 
            class="btn btn-dark w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1" 
            style="font-size: 13px;">
              <img src="./image/rental-car-icon/rent-now-icon.png" alt="Rent Icon" style="width: 16px; height: 16px;">
              <span class="text-warning fw-semibold">Rent Now</span>
          </a>


          </div>
        </div>
        <!-- End -->
         <?php endforeach;?>
        <?php endif; ?>
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
