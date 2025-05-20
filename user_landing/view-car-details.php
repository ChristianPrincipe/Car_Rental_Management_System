<?php

session_start();
require '../includes/db.php';

if(!isset($_SESSION['customer_id'])){
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

$carId = $_GET['car_id'];
$branchId = $_GET['branch_id'];

// for fetching cars
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
$sqlImages = "SELECT carImages FROM car_images WHERE car_id = :car_id 
              ORDER BY uploaded_at DESC";
$imageStmt = $pdo->prepare($sqlImages);
$imageStmt->execute(['car_id' => $carId]);
$carImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
$mainImage = $carImages[0]['carImages'] ?? null;



if (!$car) {
  echo "Car not found.";
  exit();
}

// Fetch reviews for the specific car
$sql = "SELECT review.*, customers.customer_name, customers.customer_image
        FROM review
        JOIN rentals ON review.rental_id = rentals.rental_id
        JOIN customers ON rentals.customer_id = customers.customer_id
        WHERE rentals.car_id = :car_id
        ORDER BY review.review_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['car_id' => $carId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$averageRating = 0;

if (count($reviews) > 0) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += $review['rating'];
    }
    $averageRating = $totalRating / count($reviews);
    // Optional: round to 1 decimal place
    $averageRating = round($averageRating, 1);
}

// for reviews
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

      <!-- Preload critical car images
    <link rel="preload" as="image" href="image/rental-car-icon/car-example.png" />
    <link rel="preload" as="image" href="image/rental-car-icon/car-example-2.png" />
    <link rel="preload" as="image" href="image/rental-car-icon/car-example-3.png" />
    <link rel="preload" as="image" href="image/rental-car-icon/car-example-4.png" /> -->


</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
    <a href="user-dashboard.php">
      <img src="./image/nav-bar-icon/dashboard-icon.png" alt="Dashboard Icon">
      <span>Dashboard</span>
    </a>
    <a href="car-owner-shop-view.php">
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
      <a href="user-profile.php" class="text-decoration-none text-dark">
        <div class="d-flex align-items-center mb-2 px-2 py-2 m-2 hover-effect">
          <img src="./image/nav-bar-icon/profile-icon.png" alt="Profile" width="20" height="20" class="me-2">
          <span>Profile</span>
        </div>
      </a>
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
    <h4>Car Details</h4>

    <div class="lading-page-content">
      
<!-- back button -->
          
      <a href="car-owner-shop-view.php?car_id=<?php echo $carId; ?>&branch_id=<?php echo $branchId; ?>" style="all: unset; cursor: pointer; display: inline-block;">
  <img src="./image/rental-car-icon/back-button.png" alt="Back" style="width: 20px; height: 20px;">
</a>
    
      
      <div class="container mt-3">
        
        <!-- KOlirit -->
        <div class="container mt-0">

          <div class="container-fluid">
            <div class="row g-3 align-items-start">



             <!-- Main and Side Images Section -->
              <div class="col-12 col-md-8 d-flex flex-column flex-md-row">
                
                <!-- Main Image -->
                <div class="main-image-wrapper flex-grow-1 mb-3 mb-md-0 me-md-3">
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
                <div class="p-3 bg-light rounded shadow-sm h-100">
                  <h5 class="text-dark fw-bold mb-1">Description</h5>
                  <hr class="mt-1 mb-2">
                  <p class="responsive-text mb-2 text-dark">
                  <?php echo htmlspecialchars($car['car_description']); ?>
                  </p>
                  <div class="mt-4">
                    <p class="mb-1 mb-3"><strong>Price: ₱<?php echo htmlspecialchars($car['price']);?>
                    
                    <a href="booking-first-process.php?from=view-car-details.php&car_id=<?php echo $carId; ?>&branch_id=<?php echo $branchId; ?>" 
                       class="btn btn-dark w-100 d-flex justify-content-center align-items-center gap-2 rounded px-3 py-2"
                       style="font-size: 14px;">
                      <img src="./image/rental-car-icon/rent-now-icon.png" alt="Rent Icon" style="width: 16px; height: 16px;">
                      <span class="text-warning fw-semibold">Book Now</span>
                    </a>
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
  <div class="d-flex align-items-center" style="gap: 4px;">
    <div class="text-muted" style="font-size: 16px;">☆☆☆☆☆</div>
      <span class="badge bg-light text-dark border rounded-pill" style="font-size: 12px;">0/5</span>
  </div>
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
                  <p class="mb-0"><?php echo htmlspecialchars($car['capacity']);?></p> 
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


        <!-- <div class="d-flex flex-wrap align-items-start gap-3">
          <img src="./image/rental-car-icon/sample-profile.png" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="User profile">
          
          <div class="flex-grow-1" style="max-width: 500px;">
            <span class="fw-bold d-block">Elon Musk</span>
            <p class="mb-0 text-break">
              jaksdnkasdk kahdaks ankdnsakjdnbakjdbadbakdbakdbaksdbakdb askdbakdd
            </p>
            <hr>
          </div>

          
        </div>

        <div class="d-flex flex-wrap align-items-start gap-3">
          <img src="./image/rental-car-icon/sample-profile.png" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="User profile">
          
          <div class="flex-grow-1" style="max-width: 500px;">
            <span class="fw-bold d-block">Elon Musk</span>
            <p class="mb-0 text-break">
              jaksdnkasdk kahdaks ankdnsakjdnbakjdbadbakdbakdbaksdbakdb askdbakdd
            </p>
          </div>

          
        </div> -->

      </div>
      
      </div>

    </div>

  <script>
    const dropdownMenu = document.getElementById('dropdownMenu');
    const accountInfo = document.getElementById('accountInfo');

    accountInfo.addEventListener('click', function(event) {
      event.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function(event) {
      if (!accountInfo.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
      }
    });

    // return on the page where the rent now is pressed
    function goBack() {
  const params = new URLSearchParams(window.location.search);
  const from = params.get('from');  // Get 'from' from the query parameters
  const branchId = params.get('branch_id');  // Get 'branch_id' from the query parameters

  let redirectUrl = from ? from + '.php' : 'user-dashboard.php';  // Default to user-dashboard

  // If branch_id exists, append it to the redirect URL
  if (branchId) {
    redirectUrl += '?branch_id=' + branchId;
  }

  window.location.href = redirectUrl;  // Redirect to the correct page with branch_id
}



    function changeImage(imageSrc) {
      // Change the main image source when side image is clicked
      document.getElementById("mainImage").src = imageSrc;
    }

    console.log(new URLSearchParams(window.location.search).get("from"));
</script>



  <!-- Optional JS -->

  <!-- <script src="script.js"></script> -->
</body>
</html>


<style>

  *{
    font-family: sans-serif;
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

