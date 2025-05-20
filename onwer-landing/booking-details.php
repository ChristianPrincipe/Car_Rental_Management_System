<?php
session_start();
require '../includes/db.php';

// Get rental_id from URL
$rentalId = isset($_GET['rental_id']) ? intval($_GET['rental_id']) : 0;

if ($rentalId <= 0) {
    die("Invalid rental ID.");
}

// Fetch full rental booking details including car_description
$sql = "SELECT 
    r.rental_id,
    r.estimated_total,
    r.booking_date,
    r.booking_time,
    r.number_person,

    -- Customer Info
    c.customer_name,
    c.customer_emailAddress,
    c.customer_contactNumber,
    c.customer_address,
    c.zip_code,

    -- Car Info
    car.car_id,
    car.car_model,
    car.capacity,
    car.price,
    car.car_description,
    car.AC,
    ct.carType_name,
    tt.transmissionType_name,
    ft.fuelType_name,

    -- Driver Info
    d.driver_name,
    d.drivers_age,
    d.driverslicense_number,
    d.driverlicense_image,
    d.drivers_contactNumber,
    d.drivers_price,
    d.drivers_picture,

    -- Drivers Type
    dt.driversType_name,

    -- Proof of Residency
    por.proofOfResidency_name,
    por.proofOfResidency_image,

    -- Rental Type
    rt.rentalType_name,

    -- Rental Period
    rp.start_date,
    rp.return_date,
    rp.start_time,
    rp.return_time,

    -- Locations
    l.location_delivery,
    l.location_return

FROM rentals r
INNER JOIN customers c ON r.customer_id = c.customer_id
INNER JOIN cars car ON r.car_id = car.car_id
LEFT JOIN drivers d ON r.drivers_id = d.drivers_id
LEFT JOIN driverstype dt ON d.driverType_id = dt.driversType_id
LEFT JOIN proofOfResidency por ON d.proofOfResidency_id = por.proofOfResidency_id
INNER JOIN rentaltype rt ON r.rentalType_id = rt.rentalType_id
INNER JOIN rentalperiods rp ON r.rentalPeriod_id = rp.rentalPeriod_id
INNER JOIN locations l ON r.locations_id = l.location_id
INNER JOIN cartype ct ON car.cartype_id = ct.carType_id
INNER JOIN transmissionType tt ON car.transmissionType_id = tt.transmissionType_id
INNER JOIN fuelType ft ON car.fuelType_id = ft.fuelType_id

WHERE r.rental_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$rentalId]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found.");
}


// Fetch car images
$sqlImages = "SELECT carImages FROM car_images WHERE car_id = :car_id";
$stmtImages = $pdo->prepare($sqlImages);
$stmtImages->execute(['car_id' => $booking['car_id']]);
$carImages = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
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
        <h4>Booking Details</h4>


        <!-- lading-page-content" -->
            <div class="lading-page-content">

                <!-- back button -->
                <a onclick="history.back()" style="border: none;">
                    <img src="./image/rental-car-icon/back-button.png" alt="Back" style="width: 20px; height: 20px;">
                </a>
                
                <hr>

                    <div class="container mt-3">
                    
                    <!-- KOlirit -->
                    <div class="container mt-0">
            
                        <div class="container-fluid">
                        <div class="row g-3 align-items-start">
                        
                            
                        <!-- Main and Side Images Section -->
                            <div class="col-12 col-md-8 d-flex flex-column flex-md-row">
                            
                            <!-- Main Image -->
                            <div class="main-image-wrapper flex-grow-1 mb-3 mb-md-0 me-md-3">
                                <?php $mainImage = !empty($carImages) ? $carImages[0]['carImages'] : 'default-image.jpg'; ?>
                                <img id="mainImage" src="../uploads/<?php echo htmlspecialchars($mainImage); ?>" 
                                loading="eager" alt="Main Image" class="img-fluid w-100">
                            </div>                
            
                            <!-- Side Images -->
                            <div class="d-flex flex-md-column gap-2 flex-wrap" style="max-width: 100%;">
                                <?php foreach ($carImages as $index => $image): ?>
                          <img src="../uploads/<?php echo htmlspecialchars($image['carImages']); ?>" 
                              alt="Side Image <?php echo $index + 1; ?>" 
                              class="img-thumbnail cursor-pointer side-image" 
                              onclick="changeImage('../uploads/<?php echo htmlspecialchars($image['carImages']); ?>')">
                      <?php endforeach; ?>
                            </div>
                            
                            </div>
            
                            
                            
                            <!-- Car Description -->
                            <div class="col-12 col-md-4">
                              <!-- Total Box -->
                              <div class="border p-3 rounded mb-3 bg-white shadow-sm" style="max-width: 100%;">
                                <strong>Total: <span><?php echo htmlspecialchars($booking['estimated_total']); ?></span></strong>
                              </div>
                            
                              <!-- Description Box -->
                              <div class="p-3 bg-light rounded shadow-sm h-100">
                                <h5 class="text-dark fw-bold mb-1">Description</h5>
                                <hr class="mt-1 mb-2">
                                <p class="responsive-text mb-2 text-dark">
                                 <?php echo htmlspecialchars($booking['car_description']); ?>
                                </p>
                              </div>
                            </div>
                              
                        
                        </div>
                        </div>
                    </div>
            
                    <div class="mt-4">
            
                    
                <h3>Car Brand: <?php echo htmlspecialchars($booking['car_model']); ?></h3>
                <h5>Car Type: <?php echo htmlspecialchars($booking['carType_name']); ?></h5>  
            
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2 text-warning" style="font-size: 16px;">★★★★★</div>
                        <span class="badge bg-light text-dark border rounded-pill" style="font-size: 12px;">5.0</span>
                    </div>
            
                    </div>
            
                        <div>
                        <h3>Specification</h3>
                    
                        <div class="d-flex flex-wrap gap-3">
                            <!-- Seat Icon and Description -->
                            <div class="d-flex align-items-center">
                                <img style="width: 50px;" src="./image/rental-car-icon/big-seat-icon.png" alt="">
                                <div class="ms-2">
                                    <span><strong>Seats</strong></span>
                                    <p class="mb-0"><?php echo htmlspecialchars($booking['capacity']); ?></p>
                                </div>
                            </div>
                        
                            <!-- Transmission Icon and Description -->
                            <div class="d-flex align-items-center">
                                <img style="width: 50px;" src="./image/rental-car-icon/big-transmission-icon.png" alt="">
                                <div class="ms-2">
                                    <span><strong>Transmission</strong></span>
                                    <p class="mb-0"><?php echo htmlspecialchars($booking['transmissionType_name']); ?></p>
                                </div>
                            </div>
                        
                            <!-- Gas Type Icon and Description -->
                            <div class="d-flex align-items-center">
                                <img style="width: 50px;" src="./image/rental-car-icon/big-gas-icon.png" alt="">
                                <div class="ms-2">
                                    <span><strong>Gas Type</strong></span>
                                    <p class="mb-0"><?php echo htmlspecialchars($booking['fuelType_name']); ?></p>
                                </div>
                            </div>
                        
                            <!-- Air Condition Icon and Description -->
                            <div class="d-flex align-items-center">
                                <img style="width: 50px;" src="./image/rental-car-icon/big-ariCon-icon.png" alt="">
                                <div class="ms-2">
                                    <span><strong>Air Condition</strong></span>
                                    <p class="mb-0"><?php echo htmlspecialchars($booking['AC']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                
                </div>
            

<hr>
                <!-- Booking Detail -->
                <div class=" mt-5">

                    <form id="driverForm" action="">
                        <div class="col-md-6 w-100">
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
                                        <p><span class="section-title">Rental Type:</span> <?php echo htmlspecialchars($booking['rentalType_name']); ?></p>
                                        <p><span class="section-title">Booking Type:</span> Days</p>
                                        <p><span class="section-title">Delivery Location & Time:</span><br><?php echo htmlspecialchars($booking['location_delivery']); ?> - 
                                    <?php echo htmlspecialchars($booking['start_date']); ?>, <?php echo htmlspecialchars($booking['start_time']); ?></p>
                                        <p><span class="section-title">Return Location & Time:</span><br>M<?php echo htmlspecialchars($booking['location_return']); ?> - 
                                    <?php echo htmlspecialchars($booking['return_date']); ?>, <?php echo htmlspecialchars($booking['return_time']); ?></p>
                                        </div>
                                    </div>
                                

                                    <!-- Driver Card -->
                               <div class="col-md-4">
                                 <div class="booking-card">
                                   <div class="booking-header">
                                     <img src="./image/process-icon/streering-icon.png" alt="Driver Icon">
                                     <span>Driver</span>
                                   </div>
                                    <?php if ($booking['driversType_name'] === 'Acting Driver'): ?>
                                <h3>Acting Driver:</h3>
                                <p>Name: <?php echo htmlspecialchars($booking['driver_name']); ?></p>
                                <p>Price: <?php echo htmlspecialchars($booking['drivers_price']); ?></p>
                                <p><img src="../uploads/<?php echo htmlspecialchars($booking['drivers_picture']); ?>" alt="Driver's picture" 
                                  style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"></p>

                              <?php elseif ($booking['driversType_name'] === 'Self-Drive'): ?>
                                <h3>Self Drive:</h3>
                                <p>Full Name: <?php echo htmlspecialchars($booking['driver_name']); ?></p>
                                <p>Age:  <?php echo htmlspecialchars($booking['drivers_age']); ?></p>
                                <p>Mobile:  <?php echo htmlspecialchars($booking['drivers_contactNumber']); ?></p>
                                <p>License:  <?php echo htmlspecialchars($booking['driverslicense_number']); ?></p>
                                <p>Residency:  <?php echo htmlspecialchars($booking['proofOfResidency_name']); ?></p>
                                <p><img src="../uploads/<?php echo htmlspecialchars($booking['driverlicense_image']); ?>" alt="License image" 
                                  style="width: 100px; height: 100px; object-fit: cover;"></p>
                                <p><img src="../uploads/<?php echo htmlspecialchars($booking['proofOfResidency_image']); ?>" alt="Residency image"
                                  style="width: 100px; height: 100px; object-fit: cover;"></p>
                              <?php else: ?>
                                <p>No driver data found.</p>
                              <?php endif; ?>
                                 </div>
                               </div>
                                
                                <?php
                                // Split the customer name into first name and last name
                                $nameParts = explode(' ', $booking['customer_name']);
                                $firstName = $nameParts[0];  // Assuming the first name is the first part
                                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';  // Handle case where there might be no last name
                                ?>

                                    <!-- User Information Card -->
                               <div class="col-md-4">
                                 <div class="booking-card">
                                   <div class="booking-header">
                                     <img src="./image/process-icon/personal-info-icon.png" alt="User Icon">
                                     <span>User Information</span>
                                   </div>
                                   <p><span class="section-title">First Name:</span>  <?php echo htmlspecialchars($firstName); ?></p>
                                   <p><span class="section-title">Last Name:</span>  <?php echo htmlspecialchars($lastName); ?></p>
                                   <p><span class="section-title">No. of Person:</span> <?php echo htmlspecialchars($booking['number_person']); ?></p>
                                   <p><span class="section-title">Mobile No.:</span> <?php echo htmlspecialchars($booking['customer_contactNumber']); ?></p>
                                   <p><span class="section-title">City:</span> <?php echo htmlspecialchars($booking['customer_address']); ?></p>
                                   <p><span class="section-title">Barangay:</span> <?php echo htmlspecialchars($booking['customer_address']); ?></p>
                                   <p><span class="section-title">Zip Code:</span> <?php echo htmlspecialchars($booking['zip_code']); ?></p>
                                   <p><span class="section-title">Email Address:</span> <?php echo htmlspecialchars($booking['customer_emailAddress']); ?></p>
                                 </div>
                               </div>
                                    </div>
                                </div>
                            </div>
                        
                    </form>

                    </div>
            </div>
        <!-- lading-page-content end-->

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

      function changeImage(imageSrc) {
      // Change the main image source when side image is clicked
      document.getElementById("mainImage").src = imageSrc;
    }


    function updateDriverInfo() {
      const driverType = document.getElementById('driver-type').value;
      const driverInfo = document.getElementById('driver-info');
      const selfDriveInfo = document.getElementById('self-drive-info');

      if (driverType === 'driver') {
        driverInfo.style.display = 'block';
        selfDriveInfo.style.display = 'none';
      } else if (driverType === 'self-drive') {
        driverInfo.style.display = 'none';
        selfDriveInfo.style.display = 'block';
      }
    }

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

  /* for car image */
.side-image {
  width: 100%;
  max-width: 135px;   /* Prevents growing too wide on large screens */
  height: auto;
}

.side-image:hover {
  transform: scale(1.03);
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

#mainImage {
  max-width: 50rem;   /* Adjust the main image size to match the side images */
  height: auto;
  object-fit: cover;  /* Ensures the main image maintains its aspect ratio */
  aspect-ratio: 16 / 9; /* keeps all images same shape */
}

.cursor-pointer {
  cursor: pointer;
}


.responsive-text {
  word-wrap: break-word;
  overflow-wrap: break-word;
  text-wrap: wrap;
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
