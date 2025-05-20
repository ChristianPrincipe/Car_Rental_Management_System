<?php

session_start();

require '../includes/db.php';

if(!isset($_SESSION['customer_id'])){
  header('Location: ../Login-forms/user-login.php');
  exit();
}


$customer_id = $_SESSION['customer_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$_SESSION['customer_id']]);
    $user = $stmt->fetch();

    $stmtBookings = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE customer_id = ? AND bookingStatus_id = 5");
    $stmtBookings->execute([$_SESSION['customer_id']]);
    $bookedCount = $stmtBookings->fetchColumn();

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
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
  
    <a href="user-dashboard.php">
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



  <!-- Main Content -->
  <div class="main-content">
    <h4>Car Rental Owner</h4>
    

    <!-- lading-page-content" -->
    <div class="lading-page-content">
     
         <!-- <button onclick="history.back()" style="border: none;">
            <img src="../button/back-button.png" alt="Back" style="width: 20px; height: 20px;">
        </button> -->

    <!-- Profile shown -->
      <div id="profileShown">
        <!-- Edit profile button -->  
        <div class="d-flex flex-column" style="width: 60px; align-items: center;">
          <div class="d-flex flex-column align-items-center">
            <button onclick="toggleEditForm()" style="border: none; padding: 10px; border-radius: 5px; background: none;">
              <img src="./image/nav-bar-icon/edit-icon.png" style="width: 25px; height: 25px;">
            </button>
            <span style="font-size: 11px; margin-top: 2px; text-align: center;">Edit Profile</span>
          </div>
        </div>

        <!-- Profile Information -->
        <div class="container my-2 p-4 rounded" style="background-color: #EDEDED;">
          <div class="row">
            <!-- Profile -->
            <div class="col-md-6 mb-3">
              <div class="p-3 rounded" style="background-color: #FFFFFF;">
                <div class="d-flex align-items-center flex-column mb-2">

                <?php

                  if (!empty($user['customer_image'])) {
                      // If it looks like a valid URL, use it as is
                      if (filter_var($user['customer_image'], FILTER_VALIDATE_URL)) {
                          $profileImage = $user['customer_image'];
                      } else {
                          // Otherwise, assume local file upload path
                          $profileImage = '../uploads/' . htmlspecialchars($user['customer_image']);
                      }
                  }
                  ?>
                  <img src="<?php echo $profileImage; ?>" 
                      alt="Profile Image" 
                      class="mb-3 rounded-circle" 
                      style="width: 160px; height: 160px; background-color: #ccc;">


                  <div class="text-center">
                    <h3 class="mb-2"><?php echo htmlspecialchars($user['customer_name']);?></h3>
                    <div class="d-inline-block px-5 py-1 mb-1 rounded" style="background-color: #f0f0f0; border: 1px solid #ccc;">
                      <strong><?php echo $bookedCount; ?></strong>
                    </div>
                    <br>
                    <small class="text-muted">No. Booked Car</small>
                  </div>
                </div>
              </div>
            </div>
            <!-- Personal Information -->
            <div class="col-md-6 mb-3">
              <div class="p-3 rounded" style="background-color: #FFFFFF;">
                <h5 class="mb-3">Personal Information</h5>
                <hr>
                <p><strong>Age: <?php echo htmlspecialchars($user['customer_age']);?> </strong> </p>
                <p><strong>Gender: <?php echo htmlspecialchars($user['customer_gender']);?> </strong> </p>
                <p><strong>Birthday: <?php echo htmlspecialchars($user['customer_birthdate']);?></strong> </p>
                <p><strong>Address: <?php echo htmlspecialchars($user['customer_address']);?> </strong> </p>
                <p><strong>Email Address: <?php echo htmlspecialchars($user['customer_emailAddress']);?> </strong> </p>
              </div>
            </div>
          </div>
        </div>
      </div>

   <!-- Edit Profile Form -->
      <div id="editProfileForm" class="container my-4 p-4 rounded-4 shadow" style="background-color: #f0f0f0; display: none; max-width: 900px;">
        <form action="../user_process/user_profile_edit.php" method="post" enctype="multipart/form-data">
          <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold" for="profile-image">Update Photo</label>
                <input type="file" class="form-control rounded-3 border-dark" id="profile-image" name="profile-image" placeholder="Upload Photo">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold" for="name">First Name</label>
                <input type="text" class="form-control rounded-3 border-dark" id="name" name="name" placeholder="Enter Name">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold" for="lastname">Last Name</label>
                <input type="text" class="form-control rounded-3 border-dark" id="lastname" name="lastname" placeholder="Enter Lastname">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold" for="age">Age</label>
                <input type="text" class="form-control rounded-3 border-dark" id="age" name="age"  placeholder="Enter your Age">
              </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold" for="gender">Gender</label>
                <select class="form-control rounded-3 border-dark" name="gender">
                  <option value="Male"  <?php if($user['customer_gender'] == 'Male') ?>>Male</option>
                  <option value="Female" <?php if($user['customer_gender'] == 'Female') ?>>Female</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold" for="birthday">Birthdate</label>
                <input type="date" class="form-control rounded-3 border-dark" name="birthdate">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold" for="address">Address</label>
                <input type="text" class="form-control rounded-3 border-dark" id="address" name="address" placeholder="Enter your Address">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold" for="email">Email Address</label>
                <input type="email" class="form-control rounded-3 border-dark" id="email" name="email" placeholder="Email Address">
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="d-flex justify-content-end gap-2 mt-4">
            <!-- Back Button -->
            <button type="button" class="btn btn-secondary px-4 py-2 rounded-3" onclick="toggleEditForm()">Back</button>

            <!-- Save Changes Button -->
            <button type="submit"
              class="btn btn-dark d-flex justify-content-center align-items-center gap-2 rounded-3 px-4 py-2">
              <span class="text-warning fw-semibold">Save Changes</span>
            </button>
          </div>
        </form>
      </div>




      </div><!-- lading-page-content-->
  </div>


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