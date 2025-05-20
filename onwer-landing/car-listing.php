<?php
require '../includes/db.php';
session_start(); // Ensure session is started

// Check if owner is logged in
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../login-forms/owner-login.php");
    exit;
}

$owner_id = $_SESSION['owner_id'];

// Get branch_id associated with the logged-in owner
$branchStmt = $pdo->prepare("SELECT branch_id FROM branches WHERE owner_id = ?");
$branchStmt->execute([$owner_id]);
$branch = $branchStmt->fetch(PDO::FETCH_ASSOC);
$branch_id = $branch['branch_id'] ?? null;

if (!$branch_id) {
    echo "No branch found for this owner.";
    exit;
}

// Fetch cars belonging to this branch
$sql = "
    SELECT cars.*, 
           ci.carImages, 
           cartype.carType_name, 
           transmissionType.transmissionType_name, 
           fuelType.fuelType_name
    FROM cars 
    LEFT JOIN (
        SELECT car_id, carImages
        FROM (
            SELECT car_id, carImages, ROW_NUMBER() OVER (PARTITION BY car_id ORDER BY uploaded_at) AS rn
            FROM car_images
        ) AS ranked_images
        WHERE ranked_images.rn = 1
    ) ci ON cars.car_id = ci.car_id
    INNER JOIN cartype ON cars.cartype_id = cartype.carType_id 
    INNER JOIN transmissionType ON cars.transmissionType_id = transmissionType.transmissionType_id
    INNER JOIN fuelType ON cars.fuelType_id = fuelType.fuelType_id
    WHERE cars.branch_id = ? 
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$branch_id]);
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
        <a href="#">
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
          <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
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
      <!-- Header end-->


      <!-- Main Content -->
      <div class="main-content">
        <h4>Car Listing</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content">

            <!-- Add and update button -->
            <div class="d-flex justify-content-end gap-2">
                <!-- update car -->
                <a href="update-car-first.php"
                    class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1"
                    style="font-size: 12px; width: 130px;">
                 <span class="text-warning fw-semibold">Update Car</span>
                </a>
                
                <!-- add car -->
                <a href="add-car.php"
                    class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1"
                    style="font-size: 12px; width: 130px">
                 <span class="text-warning fw-semibold">Add Car</span>
                </a>

                <!-- delete car-->
                <a href="delete-car.php"
                class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1"
                style="font-size: 12px; width: 130px">
             <span class="text-warning fw-semibold">Delete Car</span>
            </a>
            </div>

            <hr>

            <div class="d-flex flex-wrap gap-3">
                <?php foreach($cars as $car): ?>
                    <?php
                        $startDate = $returnDate = null;
                        if ($car['car_status'] === 'Not Available') {
                            $sql = "SELECT rentalperiods.start_date, rentalperiods.return_date
                                    FROM rentals
                                    JOIN rentalperiods ON rentals.rentalPeriod_id = rentalperiods.rentalPeriod_id
                                    WHERE rentals.car_id = :car_id
                                    ORDER BY rentalperiods.start_date DESC
                                    LIMIT 1";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(['car_id' => $car['car_id']]);
                            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($booking) {
                                $startDate = $booking['start_date'];
                                $returnDate = $booking['return_date'];
                            }
                        }
                    ?>
                <!-- Card body -->
                <div class="card" style="width: 200px; border: 1px solid #ddd; border-radius: 10px;">
                    <div class="card-body p-3 pt-2">
                        <!-- Image (Responsive) -->
                        <div class="mb-2">
                            <img src="../uploads/<?php echo htmlspecialchars($car['carImages']); ?>" alt="Car Image" class="img-fluid rounded" />
                        </div>

                        <!-- Car Name -->
                        <h4 class="card-title fw-bold mb-2" style="font-size: 16px;"><?php echo htmlspecialchars($car['car_model']); ?></h4>
                    
                        <!-- Vehicle Type -->
                        <div class="mb-2" style="font-size: 13px; color: #6c757d;"><?php echo htmlspecialchars($car['carType_name']); ?></div>  

                        <!-- Price -->
                        <div class="mb-2" style="font-size: 13px;">
                            <span>₱ <?php echo htmlspecialchars($car['price']); ?>/Day</span>
                        </div>
                    
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
                    
                        <!-- Rent Now Button -->
                        <a href="car-details.php?from=from-carlisting&car_id=<?php echo $car['car_id']; ?>&branch_id=<?php echo $car['branch_id']; ?>"
                            class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1"
                            style="font-size: 12px; width: 100%;">
                            <img src="./image/car-details.png" alt="Rent Icon" 
                                style="width: 12px; height: 12px;">
                            <span class="text-warning fw-semibold">Car Details</span>
                        </a>

                    </div>
                </div>
                <?php endforeach; ?>

            </div>


        </div><!-- lading-page-content end-->

      </div>
      <!-- Main Content end-->




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