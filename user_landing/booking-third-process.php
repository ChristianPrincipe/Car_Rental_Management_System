<?php
session_start();

require '../includes/db.php';

$totalPrice = $_SESSION['booking_data']['totalPrice'];
$branchId = $_SESSION['booking_data']['branch_id'];
$carId = $_SESSION['booking_data']['carId'];


if(!isset($_SESSION['customer_id'])){
  header('Location: ../user_landing/user-login.php');
  exit();

}
$customer_id = $_SESSION['customer_id'];

$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$_SESSION['customer_id']]);
$userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

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
          <?php

          if($userDetails){

            //for customer name separation
            $fullName = ($userDetails['customer_name']);
            $nameParts = explode(' ', $fullName);
            
            $firstName = $nameParts[0]; 
            $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; 

            //for address separation
            $fullAddress = ($userDetails['customer_address']);
            $addressParts = explode(' ', $fullAddress);

            $barangay = $addressParts[0];
            $city = isset($addressParts[1]) ? $addressParts[1] : '';


          ?>
<!-- Form///////////// -->
        <form id="driverForm" action="../booking_process/third_process.php" method="POST">
            <div class="col-md-6 w-100">
                <div class="card h-100 p-4">
                    <div class="card-body">
                      <div class="mb-4 d-flex align-items-center gap-2">
                        <img src="./image/process-icon/personal-info-icon.png" alt="Driver Icon" style="width: 30px; height: 30px;">
                        <span style="font-size: 20px; font-weight: bold;">User details</span>
                      </div>
                  
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">First Name: </label>
                            <input type="text" class="form-control" name="first_name" placeholder="Enter First Name" name="details_fname" value= "<?php echo htmlspecialchars($firstName); ?>" required>
                          <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" placeholder="Enter Last Name" name="details_lname" value="<?php echo htmlspecialchars($lastName); ?>" required >
                          </div>
                        </div>
                  
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">No. of Person</label>
                            <input type="text" class="form-control" name="details_noPerson" placeholder="Enter No. of Person" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Enter Phone no.</label>
                            <input type="text" class="form-control" name="details_phoneNumber" placeholder="Phone no." required>
                          </div>
                        </div>
                  
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="details_city" placeholder="Enter Address" value= "<?php echo htmlspecialchars($city); ?>" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Barangay</label>
                            <input type="text" class="form-control" name="details_barangay" placeholder="Enter Address" value= "<?php echo htmlspecialchars($barangay); ?>" required>
                          </div>
                        </div>
                  
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">Zip Code</label>
                            <input type="number" class="form-control" name="details_zipcode" placeholder="Enter Zip Code" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="details_emailAddress" placeholder="Enter Email address" value= "<?php echo htmlspecialchars($userDetails['customer_emailAddress']); ?>" required>
                          </div>
                        </div>
                  
                        <!-- <div class="mb-3">
                          <label class="form-label">Additional Information</label>
                          <textarea class="form-control" rows="4" name="details_additionalInfo" placeholder="Add additional information"></textarea>
                        </div> -->
                    </div>
                  </div>
                  

            <div class="d-flex justify-content-center gap-3 mt-5 mb-5">
              <!-- Return Button -->
              <a onclick="history.back()"
                 class="btn btn-white rounded px-3 py-2 btn-next-hover text-warning fw-semibold text-center"
                 style="font-size: 14px; width: 200px; border: 1px solid black;">
                Return
              </a>

              <button type="submit" class="btn btn-white rounded px-3 py-2 btn-next-hover text-warning fw-semibold text-center"
              style="font-size: 14px; width: 200px; border: 1px solid black;">
                Next
                </button>

            </div>
            
            </div>
        </form>




        <!-- form end -->

          </div>
          <?php
}
       ?>





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

.btn-next-hover:hover {
  background-color: #fdc650 !important; /* Yellow-ish hover */
  color: black !important;
  border-color: black !important;
}

</style>