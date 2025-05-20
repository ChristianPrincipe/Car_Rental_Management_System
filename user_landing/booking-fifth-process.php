<?php
session_start();
require '../includes/db.php';

if(!isset($_SESSION['customer_id'])){
  header('Location: ../login-forms/user-login.php');
  exit();
}

$booking_data = $_SESSION['booking_data'] ?? [];
$self_drive = $_SESSION['self_drive_data'] ?? [];
$acting_drive = $_SESSION['acting_drive_data'] ?? [];
$user_details = $_SESSION['user_details'] ?? [];


$customer_id = $_SESSION['customer_id'];

$branchId = $_SESSION['booking_data']['branch_id'];
$carId = $_SESSION['booking_data']['carId'];

// // Debug session data
// var_dump($_SESSION['acting_drive_data']);
// var_dump($_SESSION['self_drive_data']);
// exit;

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
              <div class="step-item active">
                <div class="step-label active">Step 3</div>
                <img src="image/process-icon/personal-info-icon.png">
                <div class="step-label active">User Information</div>
              </div>
              <div class="step-item active">
                <div class="step-label active">Step 4</div>
                <img src="image/process-icon/rules-icon.png">
                <div class="step-label active">Rules</div>
              </div>
              <div class="step-item active">
                <div class="step-label active">Step 5</div>
                <img src="image/process-icon/book-details-icon.png">
                <div class="step-label active">Book Details</div>
              </div>
            </div>
            
                     

            <div class="text-start col-md-2" style="width: 100%; max-width: 190px; margin-left: auto;">
              <div class="border p-3 rounded" style="width: 100%;">
                <strong>Total: ₱<span><?php echo htmlspecialchars($booking_data['totalPrice']);?></span></strong>
              </div>
            </div>
            
          </div>


<!-- Form///////////// -->
        <form id="driverForm" action="../booking_process/booking_data.php" method="POST">
            <div class="col-md-6 w-100">
                <div class="card h-100 p-4">
                    <div class="card-body">
                      <div class="mb-4 d-flex align-items-center gap-2">
                        <img src="./image/process-icon/book-details-icon.png" alt="Driver Icon" style="width: 30px; height: 30px;">
                        <span style="font-size: 18px; font-weight: bold;">Your Booking Details</span>
                      </div>
                    
                      <div class="container py-4">
                        <div class="row g-4">
                          <!-- Location Card -->
                          <div class="col-md-4">
                            <div class="booking-card">
                              <div class="booking-header">
                                <img src="./image/process-icon/map-icon.png" alt="Location Icon">
                                <span>Location</span>
                              </div>
                              <p><span class="section-title">Rental Type:</span> <?= htmlspecialchars($booking_data['rentalType'] ?? 'N/A') ?></p>
                              <p><span class="section-title">Booking Type:</span> <?= ($booking_data['durationDays'] > 0) ? 'Days' : 'Hours' ?></p>
                              <p><span class="section-title">Delivery Location & Time:</span><br>
                                <?= htmlspecialchars($booking_data['deliveryLocation'] ?? '') ?> - 
                                <?= htmlspecialchars($booking_data['startDate'] ?? '') ?>, 
                                <?= htmlspecialchars($booking_data['startTime'] ?? '') ?>
                              </p>
                              <p><span class="section-title">Return Location & Time:</span><br>
                                <?= htmlspecialchars($booking_data['returnLocation'] ?? '') ?> - 
                                <?= htmlspecialchars($booking_data['returnDate'] ?? '') ?>, 
                                <?= htmlspecialchars($booking_data['returnTime'] ?? '') ?>
                              </p>
                            </div>
                          </div>
                      
                          <!-- Driver Type Card -->
                          <div class="col-md-4">
                            <div class="booking-card">
                              <div class="booking-header">
                                <img src="./image/process-icon/streering-icon.png" alt="Driver Icon">
                                <span>Driver</span>
                              </div>

                              <?php if (!empty($acting_drive) && $acting_drive['actingDriver'] === true): ?>
                                <h3>Acting Driver:</h3>
                                <p>Name: <?= htmlspecialchars($acting_drive['driverName']); ?></p>
                                <p>Price: <?= htmlspecialchars($acting_drive['driversPrice']); ?></p>
                                <p><img src="../uploads/<?= htmlspecialchars($acting_drive['driversPicture']); ?>" alt="Driver's picture" 
                                  style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"></p>

                              <?php elseif (!empty($self_drive) && $self_drive['driverType'] === 'Self-Drive'): ?>
                                <h3>Self Drive:</h3>
                                <p>Full Name: <?= htmlspecialchars($self_drive['fullname']); ?></p>
                                <p>Age: <?= htmlspecialchars($self_drive['driverAge']); ?></p>
                                <p>Mobile: <?= htmlspecialchars($self_drive['mobileNumber']); ?></p>
                                <p>License: <?= htmlspecialchars($self_drive['licenseNumber']); ?></p>
                                <p>Residency: <?= htmlspecialchars($self_drive['residency']); ?></p>
                                <p><img src="../uploads/<?= htmlspecialchars($self_drive['licenseImage']); ?>" alt="License image" 
                                  style="width: 100px; height: 100px; object-fit: cover;"></p>
                                <p><img src="../uploads/<?= htmlspecialchars($self_drive['residencyImage']); ?>" alt="Residency image"
                                  style="width: 100px; height: 100px; object-fit: cover;"></p>
                              <?php else: ?>
                                <p>No driver data found.</p>
                              <?php endif; ?>



                            </div>
                          </div>
                      
                          <!-- User Information Card -->
                          <div class="col-md-4">
                            <div class="booking-card">
                              <div class="booking-header">
                                <img src="./image/process-icon/personal-info-icon.png" alt="User Icon">
                                <span>User Information</span>
                              </div>
                              <p><span class="section-title">First Name:</span> <?= htmlspecialchars($user_details['fullname'] ?? 'N/A') ?></p>
                              <p><span class="section-title">No. of Person:</span> <?= htmlspecialchars($user_details['noPerson'] ?? 'N/A') ?></p>
                              <p><span class="section-title">Mobile No.:</span> <?= htmlspecialchars($user_details['phoneNumber'] ?? 'N/A') ?></p>
                              <p><span class="section-title">City:</span> <?= htmlspecialchars($user_details['city'] ?? 'N/A') ?></p>
                              <p><span class="section-title">Barangay:</span> <?= htmlspecialchars($user_details['barangay'] ?? 'N/A') ?></p>
                              <p><span class="section-title">Zip Code:</span> <?= htmlspecialchars($user_details['zipcode'] ?? 'N/A') ?></p>
                              <p><span class="section-title">Email Address:</span> <?= htmlspecialchars($user_details['emailAddress'] ?? 'N/A') ?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                  
        
            


            <div class="d-flex justify-content-center gap-3 mt-2 mb-5">
              <!-- Return Button -->
              <a onclick="history.back()"
                 class="btn btn-white rounded px-3 py-2 btn-next-hover text-warning fw-semibold text-center"
                 style="font-size: 14px; width: 200px; border: 1px solid black;">
                Return
              </a>


              <button class="btn btn-white rounded px-3 py-2 btn-next-hover text-warning fw-semibold text-center"
              style="font-size: 14px; width: 200px; border: 1px solid black;">
              Book Car
              </button>
            

            </div>
            
            </div>
        </form>




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


.btn-next-hover:hover {
  background-color: #fdc650 !important; /* Yellow-ish hover */
  color: black !important;
  border-color: black !important;
}

.booking-card {
      border: 2px solid #f2a23a;
      border-radius: 12px;
      padding: 20px;
      height: 100%;
    }

    .booking-header {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      font-size: 18px;
      margin-bottom: 15px;
    }

    .booking-header img {
      width: 28px;
      height: 28px;
    }

    .section-title {
      font-weight: 600;
      margin-top: 10px;
      margin-bottom: 4px;
    }

    .card-body p {
      margin-bottom: 8px;
    }

</style>