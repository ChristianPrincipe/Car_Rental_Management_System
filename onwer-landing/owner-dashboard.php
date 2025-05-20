<?php

session_start();

require '../includes/db.php';

$owner_id = $_SESSION['owner_id'];
$psql = "SELECT branch_image, branch_name, branch_address, branch_number FROM branches WHERE owner_id = ?";
$stmt = $pdo->prepare($psql);

try {
    $stmt->execute([$owner_id]);
    $bname = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the result is valid before accessing array elements
    if ($bname) {
        // Now it's safe to access array elements
        $branch_name = $bname['branch_name'] ?? 'Default Branch Name';
        $branch_image = $bname['branch_image'] ?? 'default-image.jpg';
        $branch_address = $bname['branch_address'] ?? 'Default Address';
        $branch_number = $bname['branch_number'] ?? '000-000-0000';
    } else {
        // Handle case where no result is found
        $branch_name = 'No branch found';
        $branch_image = 'default-image.jpg';
        $branch_address = 'No address available';
        $branch_number = 'No contact number';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


// Query to count the number of cars for the current owner
$psql = "
    SELECT COUNT(cars.car_id) AS car_count
    FROM branches
    JOIN cars ON branches.branch_id = cars.branch_id
    WHERE branches.owner_id = ?
";
$stmt = $pdo->prepare($psql);
$stmt->execute([$owner_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$carCount = $result ? $result['car_count'] : 0;


// SQL query to fetch car data
$sql = "
    SELECT 
        cars.*, 
        ci.carImages, 
        cartype.carType_name, 
        transmissionType.transmissionType_name, 
        fuelType.fuelType_name
    FROM cars 
    LEFT JOIN (
        SELECT car_id, carImages 
        FROM car_images 
        WHERE (car_id, uploaded_at) IN (
            SELECT car_id, MIN(uploaded_at) 
            FROM car_images 
            GROUP BY car_id
        )
    ) ci ON cars.car_id = ci.car_id
    INNER JOIN cartype ON cars.cartype_id = cartype.carType_id 
    INNER JOIN transmissionType ON cars.transmissionType_id = transmissionType.transmissionType_id
    INNER JOIN fuelType ON cars.fuelType_id = fuelType.fuelType_id
    WHERE cars.branch_id IN (
        SELECT branch_id FROM branches WHERE owner_id = ?
    )
    GROUP BY cars.car_id
    LIMIT 5";

$stmt = $pdo->prepare($sql);
$stmt->execute([$owner_id]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// for fetching rentals
$sql = "SELECT 
            rentals.rental_id,
            rentaltype.rentalType_name,
            bookingstatus.bookingStatus_name,
            rentals.booking_date,
            rentals.booking_time,
            rentals.estimated_total,
            cars.car_model,
            MIN(car_images.carImages) AS carImages,
            customers.customer_name,
            customers.customer_image
        FROM rentals
        JOIN cars ON rentals.car_id = cars.car_id
        JOIN branches ON cars.branch_id = branches.branch_id
        LEFT JOIN car_images ON cars.car_id = car_images.car_id
        JOIN customers ON rentals.customer_id = customers.customer_id
        JOIN rentaltype ON rentals.rentalType_id = rentaltype.rentalType_id
        JOIN bookingstatus ON rentals.bookingStatus_id = bookingstatus.bookingStatus_id
        WHERE branches.owner_id = ?
        GROUP BY rentals.rental_id
        ORDER BY rentals.booking_date DESC
        LIMIT 5";

$stmt = $pdo->prepare($sql);
$stmt->execute([$owner_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch driver information
$psql = "
    SELECT 
        d.drivers_id,
        d.driver_name, 
        d.drivers_price, 
        d.drivers_picture,
        COUNT(CASE WHEN bs.bookingStatus_name = 'Completed' THEN r.rental_id END) AS trips_completed
    FROM drivers d
    LEFT JOIN rentals r ON d.drivers_id = r.drivers_id
    LEFT JOIN bookingstatus bs ON r.bookingStatus_id = bs.bookingStatus_id
    WHERE d.owner_id = ?
    GROUP BY d.drivers_id
    LIMIT 2
";

$stmt = $pdo->prepare($psql);
$stmt->execute([$owner_id]);
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);


// counting a completed rents
$sql = "
    SELECT COUNT(*) AS completed_count
    FROM rentals
    JOIN cars ON rentals.car_id = cars.car_id
    JOIN branches ON cars.branch_id = branches.branch_id
    JOIN bookingstatus ON rentals.bookingStatus_id = bookingstatus.bookingStatus_id
    WHERE branches.owner_id = ? AND bookingstatus.bookingStatus_name = 'Completed'
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$owner_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$completedBookings = $row ? $row['completed_count'] : 0;
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
      
        <a href="#">
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
        <h4>Dashboard</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content">

          <!-- MY Profile -->
          <div id="profileShown" style="font-size: 14px;">
            
            <!-- Centering wrapper -->
            <div class="d-flex justify-content-center">
              <div class="row g-4 w-100" style="max-width: 900px;">
                
                <!-- Profile -->
                <div class="col-md-3">
                  <div class="p-3 rounded h-100 shadow" style="background-color: #FFFFFF;">
                    <div class="d-flex justify-content-center align-items-center flex-column mb-2">
                      <img src="../uploads/<?php echo htmlspecialchars($bname['branch_image']);?>" 
                           alt="Profile Image" 
                           class="mb-2 rounded-circle" 
                           style="width: 80px; height: 80px; background-color: #ccc;">
                      <h6 class="mb-1 text-center"><strong><?php echo htmlspecialchars($bname['branch_name']);?></strong></h6>
                    </div>
                    <div>
                      <div class="d-flex align-items-center mb-1">
                        <img src="./image/location.png" style="width: 12px; height: 16px; margin-right: 6px;">
                        <p class="mb-0"><?php echo htmlspecialchars($bname['branch_address']);?></p>
                      </div>
                      <div class="d-flex align-items-center">
                        <img src="./image/contact.png" style="width: 12px; height: 16px; margin-right: 6px;">
                        <p class="mb-0"><?php echo htmlspecialchars($bname['branch_number']);?></p>
                      </div>
                    </div>
                  </div>
                </div>
        
                <!-- Stats -->
                <div class="col-md-4">
                  <div class="p-3 rounded h-100 shadow" style="background-color: #FFFFFF;">
                    <div class="d-flex justify-content-center align-items-center flex-column h-100">
                      <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                        <div class="text-center">
                          <h4 style="color: #F0a500; margin-bottom: 2px;"><?php echo htmlspecialchars($carCount); ?></h4>
                          <span>Number Of Cars</span>
                        </div>
                        <div class="text-center">
                          <h4 style="color: #F0a500; margin-bottom: 2px;"><?php echo htmlspecialchars($completedBookings); ?></h4>
                          <span>Total Rentals Completed</span>
                        </div>
                        <div class="d-flex flex-column align-items-center mt-2">
                          <h6 class="mb-1"><strong>Car Rules</strong></h6>
                          <a href="view-rules.php?from=owner-dashboard" style="font-size: 12px;">View Rules</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
      
                <!-- Drivers -->
                <div class="col-md-5">
                  <div class="p-3 rounded h-100 shadow" style="background-color: #FFFFFF;">
                    <h6 class="mb-1">Drivers</h6>
                    <hr class="my-1">
                    <?php foreach ($drivers as $driver): ?>
                    <div class="d-flex align-items-center gap-2">
                      <img src="../uploads/<?php echo htmlspecialchars($driver['drivers_picture']);?>" 
                           class="rounded-circle" 
                           style="width: 40px; height: 40px; object-fit: cover;" 
                           alt="User profile">
                      <div class="flex-grow-1">
                        <span class="fw-bold d-block"><?php echo htmlspecialchars($driver['driver_name']); ?></span>
                        <p>Price:<?php echo htmlspecialchars($driver['drivers_price']); ?></p>
                       <span>Driver Trips Completed: 
                        <span><?php echo htmlspecialchars($driver['trips_completed']); ?></span>
                      </span>
                      </div>
                    </div>
                    <hr class="my-1">
                    <?php endforeach; ?>
                  </div>
                </div>
        
              </div>
            </div>
          </div>
        
          <hr>
          <!-- Recent Bookings -->
          <div class="container">
            <div class="table-responsive">
              <table class="table table-bordered table-hover text-center align-middle small-table">
                <thead>
                  <tr>
                    <th>Car Name</th>
                    <th>Rental Type</th>
                    <th>Renter</th>
                    <th>Booked Date</th>
                    <th>Total</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 180px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                   <?php foreach ($bookings as $booking): ?>
                  <tr>
                    <td>
                      <img src="../uploads/<?php echo htmlspecialchars($booking['carImages']); ?>" style="width: 60px; height: 30px;"> 
                      <span><?php echo htmlspecialchars($booking['car_model']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($booking['rentalType_name']); ?></td>
                    <td>
                      <img src="../uploads/<?php echo htmlspecialchars($booking['customer_image']); ?>" style="width: 30px; height: 30px;">  
                      <span><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?>, <br>
                    <?php echo htmlspecialchars($booking['booking_time']); ?></td>
                    <td><?php echo htmlspecialchars($booking['estimated_total']); ?></td>
                  
                    <!-- Status Cell -->
                    <td>
                      <span class="badge status-badge bg-warning">
                        <?php echo htmlspecialchars($booking['bookingStatus_name']); ?></span>
                    </td>
                  
                    <!-- Actions -->
                    <td class="d-flex justify-content-center gap-1">

                      <!-- Completed Button (hidden by default) -->
                      <button class="btn btn-primary btn-sm px-2 py-0 completed-btn d-none" title="Mark as Completed" data-id="<?= $booking['rental_id']; ?>" style="font-size: 11px;" >Completed</button>
                      
                      <!-- Approve Button -->
                      <button class="btn btn-success btn-sm px-2 py-0 approve-btn" title="Approve" data-id="<?= $booking['rental_id']; ?>">✔</button> 
                  
                      <!-- Reject Button -->
                      <button class="btn btn-danger btn-sm px-2 py-0 reject-btn" title="Reject" data-id="<?= $booking['rental_id']; ?>">✖</button>
                  
                      <!-- View Button -->
                     <a href="booking-details.php?rental_id=<?= $booking['rental_id']; ?>" class="btn btn-dark btn-sm px-3 py-1" style="font-size: 10px;"> 
                        <span class="text-warning fw-semibold">View</span>
                      </a>

                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- View more text -->
              <div class="text-end">
                <a href="booked-cars.php" style="font-size: 12px;">View More</a>
              </div>
            </div>
          </div>

          <hr>
          <!-- Car List -->

           
           <div class="d-flex flex-wrap gap-3">

            <?php foreach ($cars as $car) : ?>
            <!-- Card body -->
            <div class="card" style="width: 200px; border: 1px solid #ddd; border-radius: 10px;">
              <div class="card-body p-3 pt-2">
                  
                  <!-- Image (Responsive) -->
                <div class="mb-2">
                  <img src="../uploads/<?php echo htmlspecialchars($car['carImages']); ?>" alt="Peso" class="img-fluid rounded" />
                </div>
  
                <!-- Car Name -->
                <h4 class="card-title fw-bold mb-2" style="font-size: 16px;"><?php echo htmlspecialchars($car['car_model'])?></h4>
            
                <!-- Vehicle Type -->
                <div class="mb-2" style="font-size: 13px; color: #6c757d;"><?php echo htmlspecialchars($car['carType_name']);?></div>  
            
            
                <!-- Price -->
                <div class="mb-2" style="font-size: 13px;">
                  <span>₱ <?php echo htmlspecialchars($car['price'])?></span>
                </div>
            
                 <!-- Status -->
                        <div class="mb-3">
                            <span class="fw-semibold" style="font-size: 12px;">Status:</span>
                            <?php if ($car['car_status'] === 'Available'): ?>
                              <span class="badge bg-success text-white" style="font-size: 11px;">Available</span>
                          <?php else: ?>
                              <span class="badge bg-danger text-white" style="font-size: 11px;">Not Available</span>
                          <?php endif; ?>
                        </div>
                    
            
                <!-- Car details -->
                
                <a href="car-details.php?from=owner-dashboard&car_id=<?php echo $car['car_id']; ?>&branch_id=<?php echo $car['branch_id']; ?>"
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
            
            
           </div>
          
          <!-- View more text -->
          <div class="text-end">
            <a href="car-listing.php" style="font-size: 12px;">View More</a>
          </div>
        </div> <!-- lading-page-content end -->
        
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

      // Switch Check and X button
      // Handle button visibility based on status
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("tr").forEach(function(row) {
      const status = row.querySelector(".status-badge")?.textContent?.trim();
      const approveBtn = row.querySelector(".approve-btn");
      const rejectBtn = row.querySelector(".reject-btn");
      const completedBtn = row.querySelector(".completed-btn");

      if (status === "Pending") {
        approveBtn?.classList.remove("d-none");
        rejectBtn?.classList.remove("d-none");
        completedBtn?.classList.add("d-none");
      } else if (status === "Approved") {
        approveBtn?.classList.add("d-none");
        rejectBtn?.classList.add("d-none");
        completedBtn?.classList.remove("d-none");
      } else {
        approveBtn?.classList.add("d-none");
        rejectBtn?.classList.add("d-none");
        completedBtn?.classList.add("d-none");
      }
    });
  });

  
    // Button logic for Approve, Reject, Completed with confirmation
document.addEventListener("DOMContentLoaded", function () {
  // Approve Button
  document.querySelectorAll(".approve-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const rentalId = this.dataset.id;
      if (confirm("Are you sure you want to approve this booking?")) {
        fetch("../owner_process/updateBooking_status.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: `action=approve&rental_id=${rentalId}`
        })
        .then(res => res.text())
        .then(response => {
          alert(response);
          location.reload();
        })
        .catch(error => console.error("Error:", error));
      }
    });
  });

  // Reject Button
  document.querySelectorAll(".reject-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const rentalId = this.dataset.id;
      if (confirm("Are you sure you want to reject this booking?")) {
        fetch("../owner_process/updateBooking_status.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: `action=reject&rental_id=${rentalId}`
        })
        .then(res => res.text())
        .then(response => {
          alert(response);
          location.reload();
        })
        .catch(error => console.error("Error:", error));
      }
    });
  });

  // Completed Button
  document.querySelectorAll(".completed-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const rentalId = this.dataset.id;
      if (confirm("Mark this booking as completed?")) {
        fetch("../owner_process/updateBooking_status.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: `action=complete&rental_id=${rentalId}`
        })
        .then(res => res.text())
        .then(response => {
          alert(response);
          location.reload();
        })
        .catch(error => console.error("Error:", error));
      }
    });
  });
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

  #profileShown {
      font-size: 14px;
    }
    #profileShown h1,
    #profileShown h2,
    #profileShown h3,
    #profileShown h4,
    #profileShown h5,
    #profileShown h6 {
      font-size: 20px;
    }

  .small-table td,
.small-table th {
  padding: 0.3rem !important; /* optional for tighter spacing */
  font-size: 12px;
}

</style>