<?php

require '../includes/db.php';
session_start();

if(!isset($_SESSION['owner_id'])){
  header('Location: ../Login-forms/owner-login.php');
  exit();
}

if (!isset($_GET['car_id'])) {
  echo "No car selected.";
  exit();
}
else if(!isset($_GET['branch_id'])) {
  echo "No branch selcted.";
  exit();
}

$carId = $_GET['car_id'];
$branchId = $_GET['branch_id'];



$sql = "SELECT cars.*, 
               car_images.carImages,
               cartype.carType_name, 
               transmissionType.transmissionType_name, 
               fuelType.fuelType_name
        FROM cars 
        LEFT JOIN car_images ON cars.car_id = car_images.car_id
        INNER JOIN cartype ON cars.cartype_id = cartype.carType_id 
        INNER JOIN transmissionType ON cars.transmissionType_id = transmissionType.transmissionType_id
        INNER JOIN fuelType ON cars.fuelType_id = fuelType.fuelType_id
        WHERE cars.car_id = :car_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['car_id' => $carId]);

// To fetch all images
$car = $stmt->fetch(PDO::FETCH_ASSOC);

// If car_images contains multiple images, you may need to fetch all associated images
$sqlImages = "SELECT carImages FROM car_images WHERE car_id = :car_id";
$imageStmt = $pdo->prepare($sqlImages);
$imageStmt->execute(['car_id' => $carId]);
$carImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);


if (!$car) {
  echo "Car not found.";
  exit();
}

// Fetch reviews for the specific car
$sql = "SELECT review.*, customers.customer_name, customer_image
        FROM review
        JOIN rentals ON review.rental_id = rentals.rental_id
        JOIN customers ON rentals.customer_id = customers.customer_id
        WHERE rentals.car_id = ?
        ORDER BY review.review_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$carId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// for ratings
$averageRating = 0;
if (count($reviews) > 0) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += $review['rating'];
    }
    $averageRating = $totalRating / count($reviews);
    // round to 1 decimal place
    $averageRating = round($averageRating, 1);
}
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
        <h4>Car Details</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content">

          <a id="backButton" href="#" style="display: inline-block; cursor: pointer; margin-bottom: 20px;">
            <img src="./image/back-button.png" alt="Back" style="width: 20px; height: 20px;">
          </a>

            <div class="container mt-4">

                <div class="container-fluid">
                  <div class="row g-3 align-items-start">
                
                    
                   <!-- Main and Side Images Section -->
                    <div class="col-12 col-md-8 d-flex flex-column flex-md-row">
                      
                      <!-- Main Image -->
                      <div class="main-image-wrapper flex-grow-1 mb-3 mb-md-0 me-md-3">
                        <img id="mainImage" src="../uploads/<?php echo htmlspecialchars($car['carImages']);?>" 
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
                      <div class="p-3 bg-light rounded shadow-sm h-100">
                        <h5 class="text-dark fw-bold mb-1">Description</h5>
                        <hr class="mt-1 mb-2">
                        <p class="responsive-text mb-2 text-dark">
                          <?php echo htmlspecialchars($car['car_description']); ?>
                        </p>
                        <div class="mt-4">
                          <p class="mb-1 mb-3"><strong>Price:</strong>₱<?php echo htmlspecialchars($car['price']);?></p>
                        </div>
                      </div>
                    </div>
                
                  </div>
                </div>
            </div>
      
            <div class="mt-4">
      
             
              <h3>Car Brand: <?php echo htmlspecialchars($car['car_model']);?></h3>
        <h5>Car Type: <?php echo htmlspecialchars($car['carType_name']);?></h5>  
      
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
            <p class="text-muted">☆☆☆☆☆</p>
          <?php endif; ?>
      
            </div>
      
            <div>
              <h3>Specification</h3>
          
              <div class="d-flex flex-wrap gap-3">
                <!-- Seat Icon and Description -->
                <div class="d-flex align-items-center">
                    <img style="width: 50px;" src="./image/rental-car-icon/big-seat-icon.png" alt="">
                    <div class="ms-2">
                        <span><strong>Seats</strong></span>
                        <p class="mb-0"><?php echo htmlspecialchars($car['capacity']);?></p> <!-- Added mb-0 to remove extra margin below p -->
                    </div>
                </div>
            
                <!-- Transmission Icon and Description -->
                <div class="d-flex align-items-center">
                    <img style="width: 50px;" src="./image/rental-car-icon/big-transmission-icon.png" alt="">
                    <div class="ms-2">
                        <span><strong>Transmission</strong></span>
                        <p class="mb-0"><?php echo htmlspecialchars($car['transmissionType_name']);?></p>
                    </div>
                </div>
            
                <!-- Gas Type Icon and Description -->
                <div class="d-flex align-items-center">
                    <img style="width: 50px;" src="./image/rental-car-icon/big-gas-icon.png" alt="">
                    <div class="ms-2">
                        <span><strong>Gas Type</strong></span>
                       <p class="mb-0"><?php echo htmlspecialchars($car['fuelType_name']);?></p>
                    </div>
                </div>
            
                <!-- Air Condition Icon and Description -->
                <div class="d-flex align-items-center">
                    <img style="width: 50px;" src="./image/rental-car-icon/big-ariCon-icon.png" alt="">
                    <div class="ms-2">
                        <span><strong>Air Condition</strong></span>
                        <p class="mb-0"><?php echo htmlspecialchars($car['AC']);?></p>
                    </div>
                </div>
            </div>
            
          </div>
      
            <div class="mt-4">
              <hr>
              <h5><strong>Reviews</strong></h5>
              
              <!-- User profile and comment -->
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
                        <img src="../uploads/<?php echo htmlspecialchars($review['customer_image']);?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="User profile">
                        <div class="flex-grow-1" style="max-width: 500px;">
                            <span class="fw-bold d-block"><?php echo htmlspecialchars($review['customer_name']); ?></span>
                           
                            <p class="mb-1 text-break fw-normal">
                            <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                          </p>
                          
                            <div class="text-warning" style="font-size: 16px;">
                                <?php echo str_repeat('★', (int)$review['rating']); ?>
                                <?php echo str_repeat('☆', 5 - (int)$review['rating']); ?>
                            </div>
                            <p class="text-muted" style="font-size: 12px;"><?php echo date('F j, Y', strtotime($review['review_date'])); ?></p>
                            <hr>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet for this car.</p>
            <?php endif; ?>
      
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
      
         // Get the value of the 'from' parameter, know where to return

       // Get the 'from' parameter from the URL
        const params = new URLSearchParams(window.location.search);
        const fromPage = params.get("from");

        // Define a map of known pages and their corresponding URLs
        const pageMap = {
          "owner-dashboard": "owner-dashboard.php",
          "from-carlisting": "car-listing.php"
        };
      
        // Set the back button href based on the 'from' parameter
        const backBtn = document.getElementById("backButton");
        if (backBtn && fromPage && pageMap[fromPage]) {
          backBtn.href = pageMap[fromPage];
        } else {
          backBtn.href = "booked-cars.php"; // Fallback if 'from' is not found
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



</style>