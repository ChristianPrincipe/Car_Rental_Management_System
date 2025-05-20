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
        <a href="rental-shop.php">
          <img src="./image/nav-bar-icon/car-list-icon.png" alt="Car Rental Shop Icon">
          <span>Car Listing</span>
        </a>
        <a href="booking.php">
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
          <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" 
          style="width: 170px; top: 65px; right: 50px; display: none; 
          background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
          
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
        <h4>Settings</h4>


        <!-- lading-page-content" -->
        <div class="lading-page-content">
          <div class="container mt-4 p-3 bg-light rounded shadow-sm">
            <h5>Account Settings Help</h5>
            <p class="text-muted">Manage your account preferences, update your profile, and adjust system behavior here.</p>

            <ul class="list-group mb-3">
              <li class="list-group-item">
                <strong>👤 Update Profile:</strong>
              </li>
              <li class="list-group-item">
                <strong>🔒 Change Password:</strong>
              </li>
              <li class="list-group-item">
                <strong>🚪 Logout:</strong>
              </li>
              <li class="list-group-item">
                <strong>🧾 Booking History:</strong>
              </li>
              <li class="list-group-item">
                <strong>🛠 System Preferences:</strong> 
              </li>
            </ul>

            <p class="text-secondary small">Need more help? Contact support through the official website or email us at <a href="mailto:support@jjccarrentalsystem.com">support@jjccarrentalsystem.com</a>.</p>
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