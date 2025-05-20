<?php
require '../includes/db.php'; //DB connection

session_start(); // make sure session is started
$customer_id = $_SESSION['customer_id'] ?? null; //

$pending_booking = false;

if ($customer_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rentals 
    WHERE customer_id = :customer_id 
    AND rentals.reviewed = 0
    AND bookingStatus_id = 5");
    $stmt->execute(['customer_id' => $customer_id]);
    $count = $stmt->fetchColumn();

    $pending_booking = $count > 0;
}

// for searchinggss
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereClauses = [];
$params = [];

$carQuery = "
  SELECT cars.*, car_images.carImages, cartype.carType_name, transmissionType.transmissionType_name, 
         fuelType.fuelType_name, branches.branch_name, branches.branch_image
  FROM cars
  LEFT JOIN car_images ON cars.car_id = car_images.car_id
  INNER JOIN cartype ON cars.cartype_id = cartype.carType_id
  INNER JOIN transmissionType ON cars.transmissionType_id = transmissionType.transmissionType_id
  INNER JOIN fuelType ON cars.fuelType_id = fuelType.fuelType_id
  INNER JOIN branches ON cars.branch_id = branches.branch_id
";

if ($searchTerm !== '') {
  $carQuery .= " WHERE 
    cars.car_model LIKE :search OR 
    cartype.carType_name LIKE :search OR 
    transmissionType.transmissionType_name LIKE :search OR
    fuelType.fuelType_name LIKE :search OR
    branches.branch_name LIKE :search
  ";
}

if (!empty($whereClauses)) {
    $carQuery .= ($searchTerm !== '') ? ' AND ' : ' WHERE ';
    $carQuery .= implode(' AND ', $whereClauses);
}
if (isset($sortOrder)) {
    $carQuery .= ' ' . $sortOrder;
} else {
    $carQuery .= " ORDER BY cars.car_id DESC";
}
$stmt = $pdo->prepare($carQuery);

if ($searchTerm !== '') {
    $searchParam = "%$searchTerm%";
    $stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
}
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Page</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../user_landing/side-nav-bar-style/style-user.css">
</head>
<body>

      <!-- Sidebar -->
      <div class="sidebar">
        <img src="../user_landing/image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
      
        <a href="#">
          <img src="../user_landing/image/nav-bar-icon/dashboard-icon.png" alt="Dashboard Icon">
          <span>Dashboard</span>
        </a>
        <a href="../user_landing/rental-shop.php">
          <img src="../user_landing/image/nav-bar-icon/car-list-icon.png" alt="Car Rental Shop Icon">
          <span>Car Rental Shop</span>
        </a>
        <a href="../user_landing/booking.php">
          <img src="../user_landing/image/nav-bar-icon/booking-icon.png" alt="My Booking Icon">
          <span>My Booking</span>
        </a>
        <a href="#">
          <img src="../user_landing/image/nav-bar-icon/settings-icon.png" alt="Settings Icon">
          <span>Settings</span>
        </a>
      </div>

  
      

      <!-- Header -->

      
        <div class="topbar">
          <div class="search-bar">
            <form method="GET" action="../user_process/car_list.php" class="search-bar d-flex align-items-center">
            <input type="text" name="search" placeholder="Search..." required>
            <button type="submit" style="border: none; background: transparent;">
              <img src="../user_landing/image/nav-bar-icon/search-icon.png" alt="Search" />
            </button>
          </form>
          </div>
          <div class="d-flex gap-4">
          
            <a href="ratings.php" class="d-flex flex-column align-items-center position-relative" style="text-decoration: none; color: black;">
              <div class="position-relative">
                <img src="../user_landing/image/nav-bar-icon/notif-icon.png" width="23" height="25" alt="Notification">
              
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
              <img src="../user_landing/image/nav-bar-icon/account-icon.png" width="30" alt="Account">
              <span>Account</span>
            </div>
         </div>

          <!-- Profile and Logout Section -->
          <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
            <!-- Profile -->


            
            <a href="user-profile.php" class="text-decoration-none text-dark">
              <div class="d-flex align-items-center mb-2 px-2 py-2 m-2 hover-effect">
                <img src="../user_landing/image/nav-bar-icon/profile-icon.png" alt="Profile" width="20" height="20" class="me-2">
                <span>Profile</span>
              </div>
            </a>

            <!-- Logout -->
            <a href="/logout" class="text-decoration-none text-dark">
              <div class="d-flex align-items-center px-2 py-2 m-2 hover-effect">
                <img src="../user_landing/image/nav-bar-icon/logout-icon.png" alt="Logout" width="20" height="20" class="me-2">
                <span>Logout</span>
              </div>
            </a>
          </div>
        </div>
      <!-- Header end-->

            <!-- Main Content -->
      <div class="main-content">



  <h4>Car Results</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content">

        <!-- Back Button -->
  <div class="mb-3">
    <a href="../user_landing/user-dashboard.php">
            <img src="../user_landing/image/rental-car-icon/back-button.png" alt="Back" style="width: 20px; height: 20px;">
        </a>
  </div>

<!-- card car start -->
<div class="d-flex flex-wrap justify-content-center">
<?php
if ($cars) {
  $shownCars = [];

  foreach ($cars as $car) {
    $carId = $car['car_id'];

    // Skip if we've already displayed this car
    if (isset($shownCars[$carId])) {
      continue;
    }
    $shownCars[$carId] = true;

    // Get the first image for this car (you can extend this to a carousel)
    $firstImage = $car['carImages'];

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
    <div class="position-relative p-3 d-flex justify-content-center">
      <img src="../uploads/<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars($car['car_model']); ?>" 
           class="rounded" style="width: 230px; height: 140px;">
      <a href="profile.html">
        <img src="../uploads/<?php echo htmlspecialchars($car['branch_image']); ?>" alt="User" 
             class="rounded-circle border border-2 border-white position-absolute" 
             style="width: 40px; height: 40px; bottom: -10px; right: 15px;">
      </a>
    </div>

    <div class="card-body p-3 pt-2">
      <h4 class="card-title fw-bold mb-2" style="font-size: 18px;"><?php echo htmlspecialchars($car['car_model']); ?></h4>

      <div class="mb-2" style="font-size: 14px; color: #6c757d;"><?php echo htmlspecialchars($car['carType_name']); ?></div>

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

      <div class="d-flex justify-content-between mb-3">
        <div class="d-flex align-items-center" style="font-size: 14px;">
          <img src="../user_landing/image/rental-car-icon/seats-icon.png" alt="Seats" class="me-2" style="width: 20px; height: 20px;">
          <?php echo htmlspecialchars($car['capacity']) . " seater"; ?>
        </div>
        <div class="d-flex align-items-center" style="font-size: 14px;">
          <img src="../user_landing/image/rental-car-icon/transmission-icon.png" alt="Manual" class="me-2" style="width: 20px; height: 20px;">
          <?php echo htmlspecialchars($car['transmissionType_name']); ?>
        </div>
      </div>

      <div class="d-flex justify-content-between mb-3">
        <div class="d-flex align-items-center" style="font-size: 14px;">
          <img src="../user_landing/image/rental-car-icon/gas-icon.png" alt="Diesel" class="me-2" style="width: 20px; height: 20px;">
          <?php echo htmlspecialchars($car['fuelType_name']); ?>
        </div>
        <div class="d-flex align-items-center" style="font-size: 14px;">
          <img src="../user_landing/image/rental-car-icon/peso-icon.png" alt="Peso" class="me-2" style="width: 18px; height: 17px;">
          <?php echo htmlspecialchars($car['price']); ?>
        </div>
      </div>

      <a href="../user_landing/view-car-details.php?from=user-dashboard&car_id=<?php echo $car['car_id']; ?>&branch_id=<?php echo $car['branch_id']; ?>" class="btn btn-dark w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill px-3 py-1" style="font-size: 13px;">
        <img src="../user_landing/image/rental-car-icon/rent-now-icon.png" alt="Rent Icon" style="width: 16px; height: 16px;">
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
// Seat sort
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