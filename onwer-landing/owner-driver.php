<?php

session_start();

require '../includes/db.php';

$ownerId = $_SESSION['owner_id'];

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
";

$stmt = $pdo->prepare($psql);
$stmt->execute([$ownerId]);
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Page</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="./side-nav-bar-style-owner/style.css">

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
    <h4>Driver</h4>

    <!-- lading-page-content" -->
    <div class="lading-page-content">

      <div class="d-flex justify-content-between">
            <!-- back button -->
          <a href="owner-profile.php">
            <button" style="all: unset; cursor: pointer;">
              <img src="./image/back-button.png" alt="Back" style="width: 20px; height: 20px;">
            </button>
          </a>

          <!-- Add Button -->
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" onclick="toggleAddForm()">Add Driver</button>
          </div>
    
      </div>


      <!-- Add New Driver Form (Hidden by default) -->
      <div id="addDriverForm" class="d-none mb-3">

        <div class="shadow p-3 mb-5 bg-body rounded">
          <h5 class="mb-4" style="color: #f0a500; text-align: center;"><strong>Add new Driver</strong></h5>
            <!-- PHP -->
            <form action="../owner_process/addActing_driver.php" method="POST" enctype="multipart/form-data">
            <div class="d-flex gap-3">
            <input type="hidden" name="driver_type" value="acting driver">
              <!-- Name Input -->

            <div class="d-flex flex-column">
              <label for="newImage">Upload Image</label>
              <input type="file" class="form-control" name="profile-image" id="newImage" required>
            </div>

            
              <div class="d-flex flex-column">
                <label for="newName">Name</label>
                <input type="text" class="form-control" name="driver_name" id="name" required>
              </div>

              <div class="d-flex flex-column">
                <label for="price">Price</label>
                <input type="number" class="form-control" name="price" id="price" required>
              </div>

              <!-- Button Section -->
            <div class="d-flex justify-content-end mt-3">
              <button type="submit" class="btn btn-sm btn-primary mt-2" style="width: 100px; height: 35px;">Save</button>
            </div>
          </div>
          </form>
          
        </div>

        

    </div>
      
      

    <!-- Display Data -->
    <form action="../owner_process/updateActing_driver.php" method="POST" enctype="multipart/form-data">
      <table class="table align-middle">
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Trips Completed</th>
            <th scope="col">Price</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        
        <tbody>
        <?php foreach ($drivers as $index => $driver): ?>
              <tr>
                <!-- Hidden ID (for update reference) -->
                <input type="hidden" name="drivers_id[]" value="<?php echo $driver['drivers_id']; ?>">

                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img src="../uploads/<?php echo htmlspecialchars($driver['drivers_picture']); ?>"
                        class="rounded-circle"
                        style="width: 35px; height: 35px; object-fit: cover;"
                        alt="User profile"
                        id="profileImage-<?php echo $index; ?>">

                    <span class="fw-bold" id="userName-<?php echo $index; ?>">
                      <?php echo htmlspecialchars($driver['driver_name']); ?>
                    </span>

                    <input type="text"
                          class="form-control d-none"
                          name="name[]"
                          id="nameInput-<?php echo $index; ?>"
                          value="<?php echo htmlspecialchars($driver['driver_name']); ?>"
                          style="max-width: 200px;">
                  </div>
                </td>

                <td>
                  <span id="tripCount-<?php echo $index; ?>"><?php echo htmlspecialchars($driver['trips_completed']); ?></span>
                  <input type="file" class="form-control d-none" name="profile_image[]" id="imageInput-<?php echo $index; ?>">
                </td>

                <td>
                  <span id="priceText-<?php echo $index; ?>"><?php echo htmlspecialchars($driver['drivers_price']); ?></span>
                  <input type="number"
                        class="form-control d-none"
                        name="price[]"
                        id="priceInput-<?php echo $index; ?>"
                        value="<?php echo htmlspecialchars($driver['drivers_price']); ?>"
                        step="0.01"
                        style="max-width: 120px;">
                </td>

                <td class="d-flex gap-2 align-items-center p-3">
                  <button type="button"
                          class="btn btn-sm btn-outline-primary"
                          onclick="toggleEdit(<?php echo $index; ?>)">Edit</button>

                  <button type="submit"
                          class="btn btn-sm btn-success d-none"
                          id="saveBtn-<?php echo $index; ?>">Save</button>

                  <button type="button"
                          class="btn btn-sm btn-danger d-none"
                          id="cancelBtn-<?php echo $index; ?>"
                          onclick="cancelEdit(<?php echo $index; ?>)">Cancel</button>

                  <button type="button" class="btn btn-sm btn-outline-danger" 
                  onclick="confirmDelete(<?php echo $driver['drivers_id']; ?>)">Delete</button>
                </td>
              </tr>
              <?php endforeach; ?>

        </tbody>
      </table>
    </form>
    
    
    

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
    
    function toggleAddForm() {
  const form = document.getElementById("addDriverForm");
  form.classList.toggle("d-none");
  form.style.transition = "all 0.3s ease";  // Optional: Adds a smooth transition
}


function toggleEdit(index) {
  document.getElementById(`userName-${index}`).classList.toggle("d-none");
  document.getElementById(`nameInput-${index}`).classList.toggle("d-none");
  document.getElementById(`profileImage-${index}`).classList.toggle("d-none");
  document.getElementById(`imageInput-${index}`).classList.toggle("d-none");

  document.getElementById(`tripCount-${index}`).classList.toggle("d-none");
  document.getElementById(`priceText-${index}`).classList.toggle("d-none");
  document.getElementById(`priceInput-${index}`).classList.toggle("d-none");

  document.querySelector(`[onclick="toggleEdit(${index})"]`).classList.add("d-none");
  document.getElementById(`saveBtn-${index}`).classList.remove("d-none");
  document.getElementById(`cancelBtn-${index}`).classList.remove("d-none");
}

function cancelEdit(index) {
  document.getElementById(`userName-${index}`).classList.remove("d-none");
  document.getElementById(`nameInput-${index}`).classList.add("d-none");
  document.getElementById(`profileImage-${index}`).classList.remove("d-none");
  document.getElementById(`imageInput-${index}`).classList.add("d-none");

  document.getElementById(`tripCount-${index}`).classList.remove("d-none");
  document.getElementById(`priceText-${index}`).classList.remove("d-none");
  document.getElementById(`priceInput-${index}`).classList.add("d-none");

  document.querySelector(`[onclick="toggleEdit(${index})"]`).classList.remove("d-none");
  document.getElementById(`saveBtn-${index}`).classList.add("d-none");
  document.getElementById(`cancelBtn-${index}`).classList.add("d-none");
}


 function confirmDelete(driverId) {
  if (confirm("Are you sure you want to delete this record?")) {
    window.location.href = "../owner_process/deleteActing_driver.php?id=" + driverId;
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