<?php
require '../includes/db.php';
session_start();


$customer_id = $_SESSION['customer_id'];

$totalPrice = $_SESSION['booking_data']['totalPrice'];

$branchId = $_SESSION['booking_data']['branch_id'];
$carId = $_SESSION['booking_data']['carId'];

if (!isset($carId)) {
    echo "Car ID missing.";
    exit();
}

// Fetch the owner_id using the branch_id
$stmt = $pdo->prepare("
    SELECT owner_id 
    FROM branches
    WHERE branch_id = ?
");
$stmt->execute([$branchId]);
$ownerId = $stmt->fetchColumn();

if (!$ownerId) {
    echo "Owner ID not found.";
    exit();
}

// Fetch all drivers uploaded by this owner along with their type
$stmt = $pdo->prepare("
    SELECT 
    d.drivers_id,
    d.driver_name, 
    d.drivers_price, 
    d.drivers_picture, 
    dt.driversType_name,
    COUNT(CASE WHEN bs.bookingStatus_name = 'Completed' THEN r.rental_id END) AS trip_count
FROM drivers d
JOIN driverstype dt ON d.drivertype_id = dt.driversType_id
JOIN branches b ON d.owner_id = b.owner_id
LEFT JOIN rentals r ON d.drivers_id = r.drivers_id
LEFT JOIN bookingstatus bs ON r.bookingStatus_id = bs.bookingStatus_id
WHERE b.owner_id = ?
GROUP BY d.drivers_id, d.driver_name, d.drivers_price, d.drivers_picture, dt.driversType_name

");

$stmt->execute([$ownerId]);
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <a href="car-owner-shop-view.php?car_id=<?php echo $carId; ?>&branch_id=<?php echo $branchId; ?>">
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
    <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" 
    style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
    
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
    <h4>Car Rental Owner</h4>

    <!-- lading-page-content" -->
    <div class="lading-page-content">


       <!-- Main Container -->
        <div class="container py-4">

          <!-- Header Section -->
          <div class="wraper row mb-2 align-items-center flex-wrap">
            <div class="col-md-2">
              <h6><strong>Reserve your car</strong></h6>
              <p style="font-size: 12px;">Complete the following steps</p>
            </div>

            <!-- step icon -->
            <div class="col-md-8 d-flex justify-content-center step-icons gap-0 p-1">
              <div class="step-item active">
                <div class="step-label active">step 1</div>
                <img src="image/process-icon/map-icon.png">
                <div class="step-label active">Location</div>
              </div>
              <div class="step-item active">
                <div class="step-label active">Step 2</div>
                <img src="image/process-icon/streering-icon.png">
                <div class="step-label active">Driver</div>
              </div>
              <div class="step-item">
                <div class="step-label">Step 3</div>
                <img src="image/process-icon/personal-info-icon.png">
                <div class="step-label">User Information</div>
              </div>
              <div class="step-item">
                <div class="step-label ">Step 4</div>
                <img src="image/process-icon/rules-icon.png">
                <div class="step-label ">Rules</div>
              </div>
              <div class="step-item">
                <div class="step-label ">Step 5</div>
                <img src="image/process-icon/book-details-icon.png">
                <div class="step-label">Book Details</div>
              </div>
            </div>
            
                     

            <div class="text-start col-md-2" style="width: 100%; max-width: 190px; margin-left: auto;">
              <div class="border p-3 rounded" style="width: 100%;">
              <strong>Total: ₱<span><?php echo htmlspecialchars($totalPrice);?></span></strong>
              </div>
            </div>
            
          </div>

          <div class="d-flex align-items-center gap-2 mb-4">
            <img src="./image/rental-car-icon/steering-icon.png" alt="Driver Icon" style="width: 30px; height: 30px;">
            <span style="font-size: 20px; font-weight: bold;" class="mb-0">Driver Details</span>
          </div>   

<!-- Form///////////// -->
        <form id="driverForm" action="../booking_process/acting_second_process.php" method="POST">
          <input type="hidden" name="owner_id" value="<?php echo $ownerId?>">
            <div class="col-md-6 w-100">
            <div class="card h-100">
                <div class="card-body">
                <div class="row g-2 text-center">
                    <h3 class="mt-4"><strong>Choose Driver</strong></h3>
        
                    <!-- Driver Buttons -->
                    <div class="col">
                        <a href="seflDrive-booking-second-process.php" class="btn btn-outline-secondary btn-option-second driver-btn flex-column" style="cursor: pointer;">
                          <span class="circle-indicator-second"></span>
                          <span>Self Drive</span>
                        </a>                        
                      </div>

                      <div class="col">
                      <a href="#" style="text-decoration: none;">
                        <button type="button" class="btn btn-outline-secondary btn-option driver-btn flex-column">
                          <span class="circle-indicator"></span>
                          <!-- pwede guro ni gamiton ang selfe drive store sa data base -->
                          <span class="driver-label">Action Driver</span>
                      </button>
                      </a>
                      </div>
                      
                </div>
            </div>
            
                <div class="" style="display: non;">
                    <h6 style="margin-left: 20px; margin-top: 20px;"><strong>Driver Information</strong></h6>
                     <?php foreach($drivers as $driver): ?>
                      <div class="card p-3 m-3">
                        <div class="d-flex align-items-center justify-content-between">
                          <div class="d-flex align-items-center">
                            <img src="../uploads/<?php echo htmlspecialchars($driver['drivers_picture']);?>" class="rounded-circle me-3" style="width: 80px; height: 80px;" alt="Profile">
                            <div>
                              <p class="mb-1 fw-bold"><?php echo htmlspecialchars($driver['driver_name']);?></p>
                              <p class="mb-0 text-muted">
                                Number of Rides Completed: <?php echo $driver['trip_count']; ?> <br>
                                <span class="fw-semibold">Price: ₱<?php echo htmlspecialchars($driver['drivers_price']);?></span>
                              </p>
                            </div>
                          </div>
                          <div>
                            <!-- Use a submit button with driver ID as a value -->
                            <button type="submit" name="selected_driver_id" value="<?php echo $driver['drivers_id']; ?>" class="btn btn-outline-primary">
                              Select Driver
                            </button>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </form>

        <div class="d-flex justify-content-center m-5">
              <a href="booking-first-process.php?from=ActingDriver-booking-second-process.php&car_id=<?php echo $carId; ?>&branch_id=<?php echo $branchId; ?>" 
                class="btn btn-white btn-return gap-2 rounded px-3 py-2 btn-next-hover mx-2 text-warning fw-semibold" 
                style="font-size: 14px; width: 200px; border: solid 1px black; text-align: center;">
                Return
            </a>
          </div>
          


        <!-- form end -->

          </div>
          
       





        </div>

    </div>
     <!-- lading-page-content end" -->

  </div>




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


  
  .highlighted{
      background-color: #212121 !important;
    }

    .step-icons {
      flex-wrap: wrap;
      gap: 10px;
      row-gap: 0.5rem; /* or use gap only for row spacing */
      column-gap: clamp(0.25rem, 2vw, 1.5rem); /* flexible horizontal spacing */
    }

    .step-icons img {
        width: clamp(30px, 5vw, 50px);   /* Shrinks the width responsively */
        height: clamp(30px, 5vw, 50px);  /* Shrinks the height responsively */
        border-radius: clamp(5px, 1vw, 10px);  /* Dynamic border-radius */
        padding: clamp(5px, 1vw, 10px);  /* Dynamic padding */
        background: #f0f0f0;
      }


    .step-item {
      position: relative;
      padding: 0 30px;
      text-align: center;
      font-size: 11px;
      font-weight: bold;
      padding: 0 clamp(5px, 1vw, 20px); 
      flex: 0 1 auto;
    }

    .step-item.active img{
      background-color: #212121;
    }



   .btn-option {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  height: 75px;
  border: 2px solid #F0A500;
  background-color: white !important;
  color: black;
  transition: none !important;
  box-shadow: none !important;
  pointer-events: auto;
}

/* Remove hover effect on button */
.btn-option:hover {
  background-color: white !important;
  border-color: #F0A500 !important;
  color: black !important;
  box-shadow: none !important;
  cursor: default;
}

.circle-indicator {
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background-color: transparent;
  border: 2px solid #F0A500;
  flex-shrink: 0;
  transition: none !important;
}

.driver-label {
  color: #F0A500; /* Orange color */
  font-weight: 500;
}


.btn-option-second{
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  height: 75px;
  color: black;
  transition: none !important;
  box-shadow: none !important;
  pointer-events: auto;
}

.circle-indicator-second {
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background-color: transparent;
  border: 2px solid #000000;
  flex-shrink: 0;
  transition: none !important;
}

.btn-option-second:hover .circle-indicator-second {
  border-color: rgb(255, 255, 255) !important;
}

.btn-return:hover {
  background-color: #fdc650 !important; /* or any color you prefer */
  color: black !important;
}


</style>