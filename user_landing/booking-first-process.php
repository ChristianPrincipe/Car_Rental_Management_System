<?php

session_start();
require '../includes/db.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: ../Login-forms/user-login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['car_id'])) {
  echo "No car selected.";
  exit();
}
else if(!isset($_GET['branch_id'])) {
  echo "No branch selcted.";
  exit();
}

//getting id
$car_id = $_GET['car_id'];
$branch_id = $_GET['branch_id'];


//fees fetching
$sql = "SELECT c.price, f.fee_name, f.fee_amount
        FROM cars c
        LEFT JOIN fees f 
            ON c.branch_id = f.branch_id 
            AND f.branch_id = :branch_id 
            AND f.fee_name = 'Delivery Fee'
        WHERE c.car_id = :car_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'car_id' => $car_id,
    'branch_id' => $branch_id
]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);


// Check if $car is an array (i.e., data is fetched successfully)
if ($car) {
    $price = htmlspecialchars($car['price']);
    $fee_name = htmlspecialchars($car['fee_name'] ?? 'Delivery Fee');
    $fee_amount = $car['fee_amount'] ?? 0.00; // raw decimal for JavaScript
    $fee_amount_display = number_format($fee_amount, 2); // formatted for display
} else {
    // Handle the case where no data was found
    $price = 0;
    $fee_name = 'Delivery Fee';
    $fee_amount_display = '0.00';
    // Optionally, show an error message
    echo "No car data found.";
}

//for reviewd
$rsql = "
SELECT * FROM rentals
WHERE customer_id = ? 
AND rentals.reviewed = 0
AND bookingStatus_id = 5"; // 5 for completed status

$stmt = $pdo->prepare($rsql);
$stmt->execute([$customer_id]);
$pending_booking = $stmt->fetch(PDO::FETCH_ASSOC);


// branch address selfn pick up
$bsql = "SELECT branch_address, branch_name FROM branches WHERE branch_id = ?";
$stmt = $pdo->prepare($bsql);
$stmt->execute([$branch_id]);
$branch = $stmt->fetch(PDO::FETCH_ASSOC); 

$branch_address = $branch['branch_address'] ?? '';
$branch_name = $branch['branch_name'] ?? '';
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Page</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="./side-nav-bar-style/style-user.css">
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
  
    <a href="user-dashboard.php">
      <img src="./image/nav-bar-icon/dashboard-icon.png" alt="Dashboard Icon">
      <span>Dashboard</span>
    </a>
     <a href="car-owner-shop-view.php?car_id=<?php echo $car_id; ?>&branch_id=<?php echo $branch_id; ?>">
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
    <h4><?php echo htmlspecialchars($branch_name); ?></h4>

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
              <div class="step-item">
                <div class="step-label">Step 2</div>
                <img src="image/process-icon/streering-icon.png">
                <div class="step-label">Driver</div>
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
              <strong>Total: ₱<span id="totalPrice"><?php echo $price; ?></span></strong>
            </div>
          </div>

            
          </div>

<!-- Form///////////// -->
    <form id="locationForm" action="../booking_process/first_process.php" method="POST">
      <input type="hidden" name="branch_id" value="<?php echo ($branch_id); ?>">
      <input type="hidden" name="car_id" value="<?php echo ($car_id); ?>">

    
    <div class="row g-3 mb-3">
        <!-- Rental Type Card -->
        <div class="col-md-6">
          <div class="card h-100">
          <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <img src="./image/rental-car-icon/booking-car-icon.png" style="width: 25px; height: 25px;">
            <h6 class="mb-0 ms-2">Rental Type</h6>
            <input type="hidden" name="rentalType" id="rentalTypeInput">

          </div>

          <div class="row g-2 text-center">
            <h6 class="mt-4">Choose Rental Type</h6>

           

            <div class="d-flex gap-3 justify-content-between">
              <button type="button" class="btn btn-outline-secondary btn-option rental-btn flex-column" name="rentalType" id="btnDelivery" data-value="Delivery" required>
                <span class="circle-indicator"></span>
                <span>Delivery</span>
              </button>
              <button type="button" class="btn btn-outline-secondary btn-option rental-btn flex-column" name="rentalType" id="btnSelfPickup" data-value="Self-Pickup" required>
                <span class="circle-indicator"></span>
                <span>Self Pickup</span>
              </button>
            </div>

          </div>

          <!-- Delivery Fee (Initially hidden) -->
          <div class="text-center mt-4" id="deliveryFee" style="display: none;">
            <p><strong><?php echo $fee_name; ?>:</strong> <span id="feeAmount">₱<?php echo $fee_amount_display; ?></span></p>
          </div>
  </div>

          </div>
        </div>

        <!-- Location Card -->
        <div class="col-md-6">
          <div class="card h-100">
            <!-- Location Section -->
            <div class="card-body">
              
              <!-- Location Section -->
                  <div class="card-body">
                    <!-- Location Header -->
                    <div class="d-flex align-items-center mb-3">
                      <img src="./image/rental-car-icon/booking-location-icon.png" style="width: 25px; height: 25px;">
                      <h6 class="mb-0 ms-2">Location</h6>
                    </div>

                    <!-- Delivery Location Input -->
                    <div class="mb-3 d-flex align-items-end gap-2">
                      <div class="flex-grow-1">
                        <label for="deliveryLocation" id="deliveryLocationLabel" class="form-label">Delivery Location</label>
                        <input type="text" class="form-control" id="deliveryLocation" name="deliveryLocation" placeholder="Enter location" required>
                      </div>
                    </div>

                    <!-- Return Location Input -->
                    <div class="mb-3 d-flex align-items-end gap-2">
                      <div class="flex-grow-1">
                        <label for="returnLocation" class="form-label">Return Location</label>
                        <input type="text" class="form-control" id="returnLocation" name="returnLocation" placeholder="Enter location" required>
                      </div>
                    </div>


            
            </div>
          </div>
        </div>
    </div>



      <!-- Booking Type and Time -->
      <div class="card mb-4">
          <div class="card-body">
              <h6 class="mb-3">Date and Time</h6>
              <div class="row g-4">
                  <div class="col-md-6">
                      <label for="startDate" class="form-label">Start Date</label>
                      <input type="date" class="form-control" id="startDate" name="startDate" required>

                      <label for="returnDate" class="form-label mt-3">Return Date</label>
                      <input type="date" class="form-control" id="returnDate" name="returnDate" required>
                  </div>
                  <div class="col-md-6">
                      <label for="startTime" class="form-label">Start Time</label>
                      <input type="time" class="form-control" id="startTime" name="startTime" required>

                      <label for="returnTime" class="form-label mt-3">Return Time</label>
                      <input type="time" class="form-control" id="returnTime" name="returnTime" required>
                  </div>
              </div>
          </div>
      </div>

      <div class="d-flex justify-content-center gap-3 m-5">
        <!-- Return Button Styled as Link -->
        <a href="view-car-details.php?from=booking-first-process.php&car_id=<?php echo $car_id; ?>&branch_id=<?php echo $branch_id; ?>" 
           class="btn btn-white text-warning fw-semibold btn-next-hover rounded px-3 py-2 text-center"
           style="font-size: 14px; width: 200px; border: solid 1px black;">
          Return
        </a>
      
        <!-- Next Button -->
         
        <button type="submit"
                class="btn btn-white text-warning fw-semibold btn-next-hover rounded px-3 py-2"
                style="font-size: 14px; width: 200px; border: solid 1px black;">
          Next
        </button>
      </div>
      
    
    </form>


        <!-- form end -->

          </div>






        </div>

    </div>
     <!-- lading-page-content end" -->

  </div>


  <script>
 const branchAddress = <?php echo json_encode($branch_address); ?>;
  const deliveryInput = document.getElementById('deliveryLocation');
  const returnInput = document.getElementById('returnLocation');

  const rentalTypeInput = document.getElementById('rentalTypeInput'); // Hidden rentalType input field
  const btnDelivery = document.getElementById('btnDelivery');
  const btnSelfPickup = document.getElementById('btnSelfPickup');
  const deliveryFee = document.getElementById('deliveryFee');
  const feeAmount = document.getElementById('feeAmount');
 
  const deliveryLocationLabel = document.getElementById('deliveryLocationLabel');
  const totalPriceElement = document.getElementById('totalPrice');


  const basePrice = <?php echo isset($price) ? $price : 0; ?>;
  const deliveryFeeValue = <?php echo isset($fee_amount) ? $fee_amount : 0; ?>;

  

  let rentalTypeSet = false;
  let deliverySelected = false;
  let currentDurationPrice = basePrice;

  // Buttons to enable delivery or self-pickup
  const allDeliveryButtons = document.querySelectorAll('button[data-value="Delivery"]');
  const allSelfPickupButtons = document.querySelectorAll('button[data-value="Self-Pickup"]');

  // Enable location fields for Delivery
  allDeliveryButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      deliveryInput.disabled = false;
      returnInput.disabled = false;
      deliveryInput.value = '';
      returnInput.value = '';
      rentalTypeInput.value = 'Delivery'; // Set rental type to Delivery
    });
  });

  // Disable location fields for Self Pickup
  allSelfPickupButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      deliveryInput.disabled = true;
      returnInput.disabled = true;
      deliveryInput.value = branchAddress;
      returnInput.value = branchAddress;
      rentalTypeInput.value = 'Self-Pickup'; // Set rental type to Self Pickup
    });
  });

  // Delivery button logic
  btnDelivery.addEventListener('click', () => {
    deliveryFee.style.display = 'block';
    feeAmount.textContent = `₱${deliveryFeeValue.toFixed(2)}`;
    deliveryLocationLabel.textContent = 'Delivery Location';
    deliverySelected = true;
    updateTotalPrice();
  });

  // Self Pickup button logic
  btnSelfPickup.addEventListener('click', () => {
    deliveryFee.style.display = 'none';
    deliveryLocationLabel.textContent = 'Pick Up Location';
    deliverySelected = false;
    updateTotalPrice();
  });

  const rentalButtons = document.querySelectorAll('.rental-btn');

rentalButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    // Remove active from all buttons
    rentalButtons.forEach(b => b.classList.remove('active'));
    // Add active to clicked button
    btn.classList.add('active');
  });
});


  // Calculate the duration and update the total price
  const startDateInput = document.getElementById('startDate');
  const returnDateInput = document.getElementById('returnDate');
  const startTimeInput = document.getElementById('startTime');
  const returnTimeInput = document.getElementById('returnTime');

  function calculateBookingDuration() {
    const startDate = new Date(startDateInput.value + ' ' + startTimeInput.value);
    const returnDate = new Date(returnDateInput.value + ' ' + returnTimeInput.value);

    if (startDate && returnDate && returnDate > startDate) {
      const durationInMillis = returnDate - startDate;
      const durationInDays = Math.floor(durationInMillis / (1000 * 60 * 60 * 24));
      const durationInHours = Math.floor((durationInMillis % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

      const additionalChargePerDay = 100;
      const additionalChargePerHour = 20;

      currentDurationPrice = basePrice + (durationInDays * additionalChargePerDay) + (durationInHours * additionalChargePerHour);
      updateTotalPrice();
    }
  }

  // Update total price based on duration and rental type (Delivery or Self Pickup)
  function updateTotalPrice() {
    const total = deliverySelected ? currentDurationPrice + deliveryFeeValue : currentDurationPrice;
    totalPriceElement.textContent = `${total.toFixed(2)}`;
  }

  // Attach event listeners to the inputs
  startDateInput.addEventListener('change', calculateBookingDuration);
  startTimeInput.addEventListener('change', calculateBookingDuration);
  returnDateInput.addEventListener('change', calculateBookingDuration);
  returnTimeInput.addEventListener('change', calculateBookingDuration);
</script>



  <!-- Optional JS -->
   <script src="../script.js"></script>
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

  .btn-next-hover {
    background-color: white; /* default background */
    transition: background-color 0.2s ease;
  }

  .btn-next-hover:hover {
    background-color: #e0e0e0; /* light gray on hover */
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

    .step-label.active{
      color: #F0A500;
    }

    .btn-option {
      width: 100%;
    }

    .next-btn {
      background: #FFA500;
      color: white;
    }

    .card h6 {
      font-weight: bold;
    }

    

    .btn-option {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        height: 75px; /* keep consistent height */ 
        border: 2px solid #dee2e6;
        background-color: white !important;
      }

      .btn-option:hover {
        background-color:gray !important;
        color: white;
        border-color: #6c757d;
      }


      /* Remove Bootstrap default hover background */
      .btn-option:focus,
      .btn-option:active {
        background-color: white;
        border-color: #FFA500; 
        color:#FFA500;
        box-shadow: none;
      }

      .circle-indicator {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background-color: white;
        border: 2px solid #999;
        /* Prevent the circle from stretching */
        flex-shrink: 0; 
      }

      .rental-btn.active {
        border-color: #FFA500 !important;
        color: #FFA500;
        background-color: white;
      }

      .rental-btn.active .circle-indicator {
        border-color: #FFA500;
      }

      .amount-btn.active {
        border-color: #FFA500 !important;
        color: #FFA500;
      }

      .amount-btn.active .circle-indicator {
        border-color: #FFA500;
      }

      .btn-next-hover:hover {
        background-color: #fdc650 !important; /* Hover background color */
        color: black !important;              /* Text color on hover */
        border-color: black !important;       /* Border remains black */
        transition: background-color 0.3s ease, color 0.3s ease;
      }
      
      

</style>