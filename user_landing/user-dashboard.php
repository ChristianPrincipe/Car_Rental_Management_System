<?php

require '../includes/db.php';
session_start();

if(!isset($_SESSION['customer_id'])){
  header('Location: ../Login-forms/user-login.php');
  exit();
}


$customer_id = $_SESSION['customer_id'];
//  balikan rataka
$sortOrder = "ORDER BY RAND()"; // default


$whereClauses = [];
$params = [];

if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'low_to_high') {
        $sortOrder = "ORDER BY cars.price ASC";
    } elseif ($_GET['sort'] === 'high_to_low') {
        $sortOrder = "ORDER BY cars.price DESC";
    }
}

// Filter: Vehicle Type
if (isset($_GET['vehicle_type'])) {
    $whereClauses[] = 'cartype.carType_name = :vehicle_type';
    $params[':vehicle_type'] = $_GET['vehicle_type'];
}

// Filter: Gearshift
if (isset($_GET['gearshift'])) {
    $whereClauses[] = 'transmissionType.transmissionType_name = :gearshift';
    $params[':gearshift'] = $_GET['gearshift'];
}

// Filter: Passengers
if (isset($_GET['passengers'])) {
    if ($_GET['passengers'] === '6+') {
        $whereClauses[] = 'cars.capacity >= 6';
    } else {
        $whereClauses[] = 'cars.capacity = :passengers';
        $params[':passengers'] = $_GET['passengers'];
    }
}

// for search
$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

//for cars
$sql = "
SELECT cars.*, 
       car_images.carImages, 
       cartype.carType_name, 
       transmissionType.transmissionType_name, 
       fuelType.fuelType_name, 
       branches.branch_name, 
       branches.branch_id,
       branches.branch_image
       
FROM cars
LEFT JOIN (
    SELECT car_id, carImages
    FROM (
        SELECT car_id, carImages,
               ROW_NUMBER() OVER (PARTITION BY car_id ORDER BY uploaded_at ASC) as rn
        FROM car_images
    ) ranked_images
    WHERE rn = 1
) car_images ON cars.car_id = car_images.car_id
INNER JOIN cartype ON cars.cartype_id = cartype.carType_id
INNER JOIN transmissionType ON cars.transmissionType_id = transmissionType.transmissionType_id
INNER JOIN fuelType ON cars.fuelType_id = fuelType.fuelType_id
INNER JOIN branches ON cars.branch_id = branches.branch_id
$whereSQL
$sortOrder
LIMIT 8
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <link rel="stylesheet" href="side-nav-bar-style/style-user.css">
</head>
<body>

      <!-- Sidebar -->
      <div class="sidebar">
        <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
      
        <a href="#">
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
                <!-- Show Red Badge if there is a completed booking -->
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
      <!-- Header end-->


      <!-- Main Content -->
      <div class="main-content">
        <h4>Dashboard</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content">
          
          <!-- Carousel -->
          <div class="container my-4" style="max-width: 1120px;">
              <div id="carShowcaseCarousel" class="carousel carousel-dark slide carousel-fade" 
                data-bs-ride="carousel" 
                data-bs-interval="5000" 
                data-bs-wrap="true">
          
              <div class="carousel-indicators">
                <button type="button" data-bs-target="#carShowcaseCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carShowcaseCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carShowcaseCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
              </div>
          
              <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="5000">
                  <img src="./image/carousel/first-slide.png" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item" data-bs-interval="4500">
                  <img src="./image/carousel/second-slide.png" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                  <img src="./image/carousel/third-slide.png" class="d-block w-100" alt="...">
                </div>
              </div>
          
              <button class="carousel-control-prev" type="button" data-bs-target="#carShowcaseCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carShowcaseCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
          <!-- Carousel end-->

          
          <!-- Drop down -->
          <div class="drop-buttons position-sticky d-flex justify-content-center p-0 m-0 mb-3">
              <div class="dropdown-container d-flex flex-wrap gap-4 justify-content-center px-1 py-1">

                <!-- Sort By -->
              <div class="dropdown">
                <button class="btn btn-light rounded-pill px-4 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  Sort by
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="?sort=low_to_high">Low to High Price</a>
                  <a class="dropdown-item" href="?sort=high_to_low">High to Low Price</a>
                </div>
              </div>

                <!-- Vehicle Type -->
                <div class="dropdown">
                <button class="btn btn-light rounded-pill px-4 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  Vehicle Type
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="?vehicle_type=Sedan">Sedan</a>
                  <a class="dropdown-item" href="?vehicle_type=SUV">SUV</a>
                  <a class="dropdown-item" href="?vehicle_type=Van">Van</a>
                </div>
              </div>

                <!-- Gearshift -->
            <div class="dropdown">
              <button class="btn btn-light rounded-pill px-4 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Gearshift
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="?gearshift=Automatic">Automatic</a>
                <a class="dropdown-item" href="?gearshift=Manual">Manual</a>
              </div>
            </div>

            <!-- Passengers -->
            <div class="dropdown">
              <button class="btn btn-light rounded-pill px-4 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Passengers
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="?passengers=2">2</a>
                <a class="dropdown-item" href="?passengers=5">5</a>
                <a class="dropdown-item" href="?passengers=6+">6+</a>
              </div>
            </div>


              </div>
          </div>
          <!-- Drop down end-->

           <!-- card car start -->
           <div class="d-flex flex-wrap justify-content-center">
              <?php

              if($cars){
                foreach($cars as $car){
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
            <!-- Start -->
            <div class="card m-2 shadow rounded-3 overflow-hidden" style="width: 260px;">
           
             <!-- Car image + Profile image -->
             <div class="position-relative p-3 d-flex justify-content-center">

               <!-- Clickable Car Image -->
                 <img src="../uploads/<?php echo htmlspecialchars($car['carImages']); ?>
                 " alt="<?php echo htmlspecialchars($car['car_model']); ?>" 
                     class="rounded" 
                     style="width: 230px; height: 140px;">
             
               <!-- Clickable Profile Image -->
               <a href="car-owner-shop-view.php?branch_id=<?php echo $car['branch_id']?>">
                  <img src="../uploads/<?php echo htmlspecialchars($car['branch_image']); ?>" alt="User" 
                     class="rounded-circle border border-2 border-white position-absolute" 
                     style="width: 40px; height: 40px; bottom: -10px; right: 15px;">
               </a>
             </div>
             
           
             <!-- Card Body -->
             <div class="card-body p-3 pt-2">
               <h4 class="card-title fw-bold mb-2" style="font-size: 18px;">
                <?php echo htmlspecialchars($car['car_model']); ?></h4>
             
               <!-- Vehicle Type -->
               <div class="mb-2" style="font-size: 14px; color: #6c757d;">
                <?php echo htmlspecialchars($car['carType_name']); ?></div>
             
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
             
               <!-- Seats & Transmission -->
               <div class="d-flex justify-content-between mb-3">
                 <div class="d-flex align-items-center" style="font-size: 14px;">
                   <img src="./image/rental-car-icon/seats-icon.png" alt="Seats" class="me-2" style="width: 20px; height: 20px;">
                   <?php echo htmlspecialchars($car['capacity']) . " seater"; ?>
                 </div>
                 <div class="d-flex align-items-center" style="font-size: 14px;">
                   <img src="./image/rental-car-icon/transmission-icon.png" alt="Manual" class="me-2" style="width: 20px; height: 20px;">
                   <?php echo htmlspecialchars($car['transmissionType_name']); ?>
                 </div>
               </div>
             
               <!-- Fuel & Price -->
               <div class="d-flex justify-content-between mb-3">
                 <div class="d-flex align-items-center" style="font-size: 14px;">
                   <img src="./image/rental-car-icon/gas-icon.png" alt="Diesel" class="me-2" style="width: 20px; height: 20px;">
                   <?php echo htmlspecialchars($car['fuelType_name']); ?>
                 </div>
                 <div class="d-flex align-items-center" style="font-size: 14px;">
                   <img src="./image/rental-car-icon/peso-icon.png" alt="Peso" class="me-2" style="width: 18px; height: 17px;">
                   <?php echo htmlspecialchars($car['price']); ?>
                 </div>
               </div>
             
               <!-- Rent Now Button -->
               <!-- Rent Now Button -->
              <a href="view-car-details.php?from=user-dashboard&car_id=<?php echo $car['car_id']; ?>&branch_id=<?php echo $car['branch_id']; ?>" class="btn btn-dark w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1" style="font-size: 13px;">
                <img src="./image/rental-car-icon/rent-now-icon.png" alt="Rent Icon" style="width: 16px; height: 16px;">
                <span class="text-warning fw-semibold">Rent Now</span>
              </a>

             </div>
           </div>
           <!-- End -->

           <?php
    }
} else {
    echo "<p>No cars available</p>";
}
?>

          </div> 
          <!-- card end-->
        

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

      // Close the dropdown when clicking outside of it
      document.addEventListener('click', function(event) {
        if (!accountInfo.contains(event.target) && !dropdownMenu.contains(event.target)) {
          dropdownMenu.style.display = 'none';
        }
      });


 
// sort price
      document.addEventListener('DOMContentLoaded', function () {
    const sortLowToHigh = document.getElementById('low-to-high');
    const sortHighToLow = document.getElementById('high-to-low');
    const cardsContainer = document.getElementById('cards-container');

    sortLowToHigh.addEventListener('click', () => sortCards('asc'));
    sortHighToLow.addEventListener('click', () => sortCards('desc'));

    function sortCards(order) {
      const cards = Array.from(cardsContainer.getElementsByClassName('card'));

      const sortedCards = cards.sort((a, b) => {
        const priceA = getPriceFromCard(a);
        const priceB = getPriceFromCard(b);
        return order === 'asc' ? priceA - priceB : priceB - priceA;
      });

      sortedCards.forEach(card => cardsContainer.appendChild(card));
    }

    function getPriceFromCard(card) {
      const priceDiv = card.querySelector('.price');
      if (!priceDiv) return 0;
      const text = priceDiv.textContent;
      const match = text.replace(/,/g, '').match(/\d+/);
      return match ? parseInt(match[0]) : 0;
    }
  });

// Car Type display sort
function filterCards(vehicleType) {
  const cards = document.querySelectorAll('#cards-container .card');
  cards.forEach(card => {
    const typeElement = card.querySelector('.car-type');
    const carType = typeElement ? typeElement.textContent.trim().toLowerCase() : '';
    if (carType === vehicleType.toLowerCase()) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}


// Sort Gear Shift
function filterByGear(gearType) {
  const cards = document.querySelectorAll('#cards-container .card');
  cards.forEach(card => {
    const gearElement = card.querySelector('.gear-shift');
    const gear = gearElement ? gearElement.textContent.trim().toLowerCase() : '';
    if (gear === gearType.toLowerCase()) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}


// seats sort
function filterByPassengers(seatCount, label) {
  const cards = document.querySelectorAll('#cards-container .card');
  cards.forEach(card => {
    // Get the seat information from the div with class 'seats'
    const seatText = card.querySelector('.seats').textContent;  // For example: '4 seats'
    const seats = parseInt(seatText, 10);  // Parse out the number of seats

    if (seatCount === 6 && seats >= 6) {
      card.style.display = 'block';
    } else if (seats === seatCount) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });

  // Update dropdown button label using class
  const seatBtn = document.querySelector('.seat-filter-btn');
  seatBtn.textContent = `Seats: ${label}`;
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
</style>