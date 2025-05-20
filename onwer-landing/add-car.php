<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit;
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
          <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" 
          style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
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
        <h4>Add New Car</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content p-5">

            <a href="car-listing.php">
                <button style="all: unset; cursor: pointer;">
                  <img src="./image/back-button.png" alt="Back" style="width: 20px; height: 20px; margin-bottom: 20px;">
                </button>
              </a>
          

              <div class="container my-2 p-0">
                <h3 class="text-center text-warning mb-4"><strong>Add New Car</strong></h3>
                <form action="../owner_process/addcar_validation.php" method="POST" enctype="multipart/form-data">
                    <div class="rounded bg-white p-2 pt-5 custom-shadow">

                      <div class="row g-5 d-flex justify-content-center"> 
                        <!-- Left Column -->
                        <div class="col-md-5">
                          <div class="mb-1"> 
                            <label class="form-label"><strong>Car Name</strong></label>
                            <input type="text" class="form-control" name="car_name" placeholder="Enter Car Name">
                          </div>
                  
                          <div class="mb-1">
                            <label class="form-label"><strong>Car Type</strong></label>
                            <input type="text" class="form-control" name="car_type" placeholder="Enter Car Type">
                          </div>
                  
                          <div class="mb-1">
                            <label class="form-label"><strong>Upload Car Image</strong></label>
                            <div class="mb-1">
                              <p class="mb-1">Main Image</p>
                              <input type="file" class="form-control" name="carMain_image" accept="image/*">
                            </div>
                            <div class="mb-1">
                              <p class="mb-1">Side Image</p>
                              <input type="file" class="form-control mb-1" name="carSide1_image" accept="image/*">
                              <input type="file" class="form-control mb-1" name="carSide2_image" accept="image/*">
                              <input type="file" class="form-control" name="carBack_image" accept="image/*">
                            </div>
                          </div>
                        </div>
                  
                        <!-- Right Column -->
                        <div class="col-md-5">
                          <div class="mb-1">
                            <label class="form-label"><strong>Car Price Daily</strong></label>
                            <input type="text" class="form-control" name="car_price" placeholder="Enter Car Price">
                          </div>
                  
                          <div class="mb-1">
                            <label class="form-label"><strong>No. of Seats</strong></label>
                            <input type="text" class="form-control" name="car_seats" placeholder="Enter Number of Seats">
                          </div>
                  
                          <div class="mb-1">
                            <label class="form-label"><strong>Transmission</strong></label>
                            <select class="form-select" name="car_transmission">
                              <option selected disabled>Choose Type Of Transmission</option>
                              <option>Automatic</option>
                              <option>Manual</option>
                            </select>
                          </div>
                  
                          <div class="mb-1">
                            <label class="form-label"><strong>Gas Type</strong></label>
                            <select class="form-select" name="car_gas">
                              <option selected disabled>Choose Gas Type</option>
                              <option>Gasoline</option>
                              <option>Diesel</option>
                            </select>
                          </div>
                  
                          <div class="mb-1">
                            <label class="form-label"><strong>Air Condition</strong></label>
                            <select class="form-select" name="car_AC">
                              <option selected disabled>Yes/No</option>
                              <option>Yes</option>
                              <option>No</option>
                            </select>
                          </div>
                        </div>
                  
                        <!-- Description -->
                        <div class="col-10 mt-1">
                          <div class="mb-1">
                            <label class="form-label"><strong>Car Description</strong></label>
                            <textarea class="form-control" name="car_description" placeholder="Enter Car Description" style="overflow:hidden; resize:none; height: 60px;" rows="1"></textarea>
                          </div>
                        </div>
                  
                        <!-- Button -->
                        <div class="col-12 text-center mt-4 mb-3">
                          <button type="submit" class="btn btn-dark" style="color: #f0a500;">Add Car</button>
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

      document.addEventListener("input", function (e) {
        if (e.target.tagName.toLowerCase() === "textarea") {
        e.target.style.height = "auto"; // Reset height
        e.target.style.height = e.target.scrollHeight + "px"; // Set to scroll height
        }
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

  label,
  input,
  select,
  textarea {
    font-size: 0.8rem !important;
  }

  /* Optional: reduce font size inside file input if needed */
  input[type="file"] {
    font-size: 0.8rem !important;
  }

  /* Optional: adjust placeholder font size as well */
  ::placeholder {
    font-size: 0.8rem;
  }

  .custom-shadow {
    box-shadow: 2px 2px 20px rgba(0, 0, 0, 0.2); /* horizontal, vertical, blur, color */
  }
  
</style>