<?php
session_start();
require '../includes/db.php';

$customerId = $_SESSION['customer_id'];

// Get status filter from query string, default to 'all'
$statusFilter = $_GET['status'] ?? 'all';

// Build WHERE clause and params array
$whereClause = "WHERE rentals.customer_id = ?";
$params = [$customerId];

if ($statusFilter !== 'all') {
    $whereClause .= " AND bookingstatus.bookingStatus_name = ?";
    $params[] = $statusFilter;
}

// Final SQL query
$bsql = "
SELECT 
    cars.car_id, 
    (SELECT carImages FROM car_images WHERE car_id = cars.car_id LIMIT 1) AS carImages,
    rentals.rental_id, 
    rentals.booking_date, 
    rentals.estimated_total, 
    bookingstatus.bookingStatus_name AS booking_status,
    cartype.carType_name AS car_type,
    cars.car_model,
    branches.branch_image,
    branches.branch_id
FROM rentals
JOIN cars ON rentals.car_id = cars.car_id
JOIN cartype ON cars.carType_id = cartype.carType_id
JOIN bookingstatus ON rentals.bookingStatus_id = bookingstatus.bookingStatus_id
JOIN branches ON cars.branch_id = branches.branch_id
$whereClause
ORDER BY rentals.booking_date DESC, rentals.rental_id DESC
";

// Prepare and execute
$stmt = $pdo->prepare($bsql);
$stmt->execute($params);
$rentedCars = $stmt->fetchAll(PDO::FETCH_ASSOC);


$rsql = "
SELECT * FROM rentals
WHERE customer_id = ?
AND rentals.reviewed = 0
AND bookingStatus_id = 5"; // 5 for Pending status

$stmt = $pdo->prepare($rsql);
$stmt->execute([$customerId]);
$pending_booking = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch one booking record if available

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
    <a href="rental-shop.php">
      <img src="./image/nav-bar-icon/car-list-icon.png" alt="Car Rental Shop Icon">
      <span>Car Rental Shop</span>
    </a>
    <a href="#">
      <img src="./image/nav-bar-icon/booking-icon.png" alt="My Booking Icon">
      <span>My Booking</span>
    </a>
    <a href="#"> 
      <!-- ../onwer-landing/settings.php -->
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
    <h4>My Booking</h4>

    <!-- lading-page-content" -->
    <div class="lading-page-content">

      <div class="nav-button d-flex flex-wrap gap-3 mb-3">
        <button class="btn bg-white text-black fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('all')">All Bookings</button>
        <button class="btn bg-white text-warning fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('pending')">Pending Approval</button>
        <button class="btn bg-white text-danger fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('rejected')">Rejected</button>
        <button class="btn bg-white text-success fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('approved')">Approved</button>
        <button class="btn bg-white text-primary fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('completed')">Completed</button>
        <button class="btn bg-white text-secondary fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('cancelled')">Cancelled</button>
        <button class="btn bg-white text-black fw-normal py-2 px-2 btn-fill-hover" style="border: 1px solid; font-size: 11px;" onclick="sortBookings('relevance')">Sort by Relevance</button>
      </div>


        <!-- Card Start -->
        <div class="d-flex flex-wrap justify-content-start">
          <?php foreach ($rentedCars as $car): ?>

            <?php
          $carId = $car['car_id'];

          // Get average rating for this car
          $sql = "SELECT rating
                  FROM review
                  JOIN rentals ON review.rental_id = rentals.rental_id
                  WHERE rentals.car_id = :car_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(['car_id' => $carId]);
          $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $averageRating = 0;
          if (count($reviews) > 0) {
              $totalRating = array_sum(array_column($reviews, 'rating'));
              $averageRating = round($totalRating / count($reviews), 1);
          }
            ?>


          <div class="card m-2 shadow rounded-3 overflow-hidden" style="width: 230px;">
            <!-- Car image + Profile image -->
            <div class="position-relative p-3 d-flex justify-content-center">
              <!-- Clickable Car Image -->
              <a href="car-details.php">
                <img src="../uploads/<?php echo htmlspecialchars($car['carImages']);?>" alt="Toyota Van" 
                    class="rounded" 
                    style="width: 200px; height: 120px; object-fit: cover;">
              </a>

              <!-- Clickable Profile Image -->
              <a href="car-owner-shop-view.php?branch_id=<?php echo $car['branch_id'] ; ?>">
                <img src="../uploads/<?php echo htmlspecialchars($car['branch_image']);?>" alt="User" 
                    class="rounded-circle border border-2 border-white position-absolute" 
                    style="width: 36px; height: 36px; bottom: -10px; right: 15px;">
              </a>
            </div>

            <!-- Card Body -->
            <div class="card-body p-3 pt-2">
              <h4 class="card-title fw-bold mb-2" style="font-size: 16px;">
                <?php echo htmlspecialchars($car['car_model']);  ?></h4>

              <!-- Vehicle Type -->
              <div class="mb-2" style="font-size: 13px; color: #6c757d;">
                <?php echo htmlspecialchars($car['car_type']);  ?></div>

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

              <!-- Price -->
              <div class="d-flex align-items-center mb-2" style="font-size: 13px;">
                <img src="./image/rental-car-icon/peso-icon.png" alt="Peso" 
                    class="me-2" style="width: 16px; height: 15px;">
                <?php echo htmlspecialchars($car['estimated_total']);  ?>
              </div>

             <!-- Status -->
              <div class="mb-3">
                <span class="fw-semibold" style="font-size: 12px;">Status:</span>
                <span class="badge 
                  <?php 
                    // Dynamically set the status badge class based on the status name
                    switch ($car['booking_status']) {
                      case 'Pending':
                        echo 'bg-warning text-dark'; // Yellow for Pending
                        break;
                      case 'Approved':
                        echo 'bg-success text-white'; // Green for Approved
                        break;
                      case 'Cancelled':
                        echo 'bg-secondary text-white'; // Grey for Cancelled
                        break;
                      case 'Rejected':
                        echo 'bg-danger text-white'; // Red for Rejected
                        break;
                      case 'Completed':
                        echo 'bg-primary text-white'; // blue for completed
                        break;
                      default:
                        echo 'bg-light text-dark'; // Default color for unknown status
                    }
                  ?>
                  " style="font-size: 11px;">
                  <?php echo htmlspecialchars($car['booking_status']); ?>
                </span>
              </div>


              <!-- Rent Now Button -->
              <a href="book-details.php?rental_id=<?php echo $car['rental_id']; ?>" 
                class="btn btn-dark w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1" 
                style="font-size: 12px;">
                <img src="./image/rental-car-icon/rent-now-icon.png" alt="Rent Icon" 
                      style="width: 14px; height: 14px;">
                <span class="text-warning fw-semibold">Book Details</span>
              </a>

            </div>
          </div>
          <?php endforeach; ?>
        </div>    
      </div> <!-- card end-->
    
      </div><!-- lading-page-content-->
  </div>

  <script>
    const dropdownMenu = document.getElementById('dropdownMenu');
    const accountInfo = document.getElementById('accountInfo');

    // Toggle dropdown visibility on account incanfo click
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

//for sortinggg
    function sortBookings(status) {
  // Create a form with the selected sorting option (could be 'all', 'inProgress', etc.)
  const form = document.createElement('form');
  form.method = 'GET'; // Use GET to send parameters in the URL (you can also use POST if needed)
  form.action = window.location.href; // Use the current URL to reload the page with parameters

  // Add the selected sort option to the form
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'status';
  input.value = status; // The value will be passed as the filter (e.g., 'all', 'inProgress')
  form.appendChild(input);

  // Append the form to the document body and submit it
  document.body.appendChild(form);
  form.submit(); // This will reload the page with the sorting option
}

    
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

  .btn-fill-hover {
    position: relative;
    overflow: hidden;
    transition: color 0.3s ease;
    z-index: 0;
  }

  .btn-fill-hover::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 0;
    background-color: currentColor;
    opacity: 0.2;
    transition: height 0.3s ease;
    z-index: -1;
  }

  .btn-fill-hover:hover::before {
    height: 100%;
  }
</style>
