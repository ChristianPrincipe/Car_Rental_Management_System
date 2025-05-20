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

// Fetch driver information
$psql = "
    SELECT 
        d.drivers_id,
        d.driver_name, 
        d.drivers_price, 
        d.drivers_picture,
        COUNT(r.rental_id) AS trips_completed
    FROM drivers d
    LEFT JOIN rentals r ON d.drivers_id = r.drivers_id
    LEFT JOIN bookingstatus bs ON r.bookingStatus_id = bs.bookingStatus_id
    WHERE d.owner_id = ?
      AND (bs.bookingStatus_name = 'Completed' OR bs.bookingStatus_name IS NULL)
    GROUP BY d.drivers_id
    LIMIT 2
";

$stmt = $pdo->prepare($psql);
$stmt->execute([$owner_id]);
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);


$branchId = $_SESSION['branch_id'];

//fees info
$fsql = "SELECT fees.fee_name, fees.fee_amount 
          FROM fees 
          JOIN branches ON fees.branch_id = fees.fee_id
          WHERE fees.branch_id = ?";
$stmt = $pdo->prepare($fsql);
$stmt->execute([$branchId]);
$fees = $stmt->fetchAll(PDO::FETCH_ASSOC);


// for counting a completd rentals
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
  <link rel="stylesheet" href="./side-nav-bar-style-owner/style.css">
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
      <span>Booked Car</span>
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

 <!-- Main Content -->
<div class="main-content">
  <h4>Car Rental Owner</h4>

  <!-- Landing-page-content -->
  <div class="lading-page-content">
    
    <!-- Profile shown -->
    <div id="profileShown">
      
      <!-- Profile Information -->
      <div class="container my-2 p-4 rounded" style="background-color: #EDEDED;">
        <div class="row">
          
          <!-- Profile -->
          <div class="col-md-5 mb-3">
            <div class="p-3 rounded h-100" style="background-color: #FFFFFF;">
              <div class="d-flex justify-content-center align-items-start gap-3 position-relative">
                
                <!-- Profile Section -->
                <div class="d-flex align-items-center flex-column mb-2">
                  <img src="../uploads/<?php echo htmlspecialchars($branch_image);?>" 
                       alt="Profile Image" 
                       class="mb-3 rounded-circle" 
                       style="width: 130px; height: 130px; background-color: #ccc;">
                  <div class="text-center">
                    <h3 class="mb-2"><strong><?php echo htmlspecialchars($branch_name); ?></strong></h3>
                  </div>
                </div>

                <!-- Edit Profile Button -->
                <div class="d-flex flex-column align-items-center position-absolute" style="right: 0;">
                  <button onclick="toggleEditForm()" 
                          style="border: none; padding: 5px; border-radius: 5px;">
                    <img src="./image/nav-bar-icon/edit-icon.png" style="width: 25px; height: 25px;">
                  </button>
                  <span style="font-size: 11px; margin-top: 2px; text-align: center;">Edit Profile</span>
                </div>
              </div>

              <div>
                <div class="d-flex">
                  <img src="./image/location.png" style="width: 17px; height: 22px; margin-right: 10px;">
                  <p><?php echo htmlspecialchars($branch_address);?></p>
                </div>
                <div class="d-flex">
                  <img src="./image/contact.png" style="width: 17px; height: 20px; margin-right: 10px;">
                  <p><?php echo htmlspecialchars($branch_number);?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats -->
          <div class="col-md-3 mb-3">
            <div class="p-3 rounded h-100" style="background-color: #FFFFFF;">
              <div class="d-flex justify-content-center align-items-center flex-column h-100">
                <div class="d-flex flex-column justify-content-center align-items-center gap-5">
                  <div class="d-flex flex-column align-items-center">
                    <h3 style="color: #F0a500;"><?php echo htmlspecialchars($carCount); ?></h3>
                    <span>Number of Cars</span>
                  </div>
                  <div class="d-flex flex-column align-items-center">
                    <h4 style="color: #F0a500; margin-bottom: 2px;"><?php echo htmlspecialchars($completedBookings); ?></h4>
                    <span>Total Rentals Completed: </span>
                  </div>
                  <div class="d-flex flex-column align-self-start">
                    <h5><strong>Car Rules</strong></h5>
                    <a href="view-rules.php?from=owner-profile" style="font-size: 12px;">View Rules</a>
                  </div>
                  <div class="d-flex flex-column align-self-start">
                    <h5><strong>Car Fees</strong></h5>
                    <a href="fees.php?from=owner-profile" style="font-size: 12px;">View fees</a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Driver Info -->
          <div class="col-md-4 mb-3">
            <div class="p-3 rounded h-100" style="background-color: #FFFFFF;">
              <div class="d-flex justify-content-between align-items-center position-relative">
                <h5>Drivers</h5>
                <!-- Edit Driver -->
                <div class="d-flex flex-column align-items-center position-absolute" style="right: 0;">
                  <a href="owner-driver.php">
                    <button onclick="toggleEditForm()" style="border: none; padding: 5px; border-radius: 5px;">
                      <img src="./image/nav-bar-icon/edit-icon.png" style="width: 25px; height: 25px;">
                    </button>
                  </a>
                  <span style="font-size: 11px; margin-top: 2px; text-align: center;">Edit Driver</span>
                </div>
              </div>
              <hr>
              <?php foreach ($drivers as $driver): ?>
              <div class="d-flex flex-wrap align-items-start gap-3">
                <img src="../uploads/<?php echo htmlspecialchars($driver['drivers_picture']); ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="User profile">
                <div class="flex-grow-1" style="max-width: 500px;">
                  <span class="fw-bold d-block"><?php echo htmlspecialchars($driver['driver_name']); ?></span>
                  <p><strong>Price:</strong><?php echo htmlspecialchars($driver['drivers_price']); ?></p>
                  <span>Driver Trips Completed: <span><span><?php echo htmlspecialchars($driver['trips_completed']); ?></span></span></span>
                </div>
              </div>
              <hr>
              <?php endforeach; ?>
            </div>
          </div>

        </div> <!-- End of .row -->
      </div> <!-- End of .container -->

    </div> <!-- End of #profileShown -->

    <!-- Edit Profile Form -->
    <div id="editProfileForm" class="container my-4 p-4 rounded-4 shadow" style="background-color: #f0f0f0; display: none; max-width: 900px;">
      <form action="../owner_process/branch_validation.php" method="post" enctype="multipart/form-data">
        <div class="row g-4">

          <div class="mb-3">
            <label class="form-label fw-bold" for="profile-image">Update Photo</label>
            <input type="file" class="form-control rounded-3 border-dark" id="profile-image" name="profile-image" placeholder="Upload Photo">
          </div>

          <!-- Left Column -->
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-bold" for="name">Car rental Name</label>
              <input type="text" class="form-control rounded-3 border-dark" id="name" name="name" placeholder="Enter Name" value="<?php echo htmlspecialchars($branch_name); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold" for="lastname">Location</label>
              <input type="text" class="form-control rounded-3 border-dark" id="lastname" name="location" placeholder="Enter Location" value="<?php echo htmlspecialchars($branch_address); ?>">
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label fw-bold" for="lastname">Number</label>
              <input type="number" class="form-control rounded-3 border-dark" id="lastname" name="number" placeholder="Enter Phone Number" value="<?php echo htmlspecialchars($branch_number); ?>">
            </div>
          </div>

        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <button type="button" class="btn btn-secondary px-4 py-2 rounded-3" onclick="toggleEditForm()">Back</button>
          <button type="submit" class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-3 px-4 py-2">
            <span class="text-warning fw-semibold">Save Changes</span>
          </button>
        </div>
      </form>
    </div>

  </div> <!-- End of .lading-page-content -->
</div> <!-- End of .main-content -->




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
    
    function toggleEditForm() {
    const form = document.getElementById('editProfileForm');
    const profile = document.getElementById('profileShown');

    if (form.style.display === 'none') {
      // Show form, hide profile
      form.style.display = 'block';
      profile.style.display = 'none';
      window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
    } else {
      // Hide form, show profile
      form.style.display = 'none';
      profile.style.display = 'block';
      window.scrollTo({ top: profile.offsetTop - 20, behavior: 'smooth' });
    }
  }


</script>

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

</style>