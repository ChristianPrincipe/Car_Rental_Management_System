<?php
session_start();


require '../includes/db.php';

// Get the branch for the owner
$ownerId = $_SESSION['owner_id'];
$stmt = $pdo->prepare("SELECT branch_id, branch_name FROM branches WHERE owner_id = ?");
$stmt->execute([$ownerId]);
$branch = $stmt->fetch(PDO::FETCH_ASSOC);
$branch_id = $branch['branch_id'] ?? 0;

// Fetch fees for this branch
$stmt = $pdo->prepare("SELECT * FROM fees WHERE branch_id = ?");
$stmt->execute([$branch_id]);
$fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fees Management</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
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
    <img src="./image/nav-bar-icon/car-list-icon.png" alt="Car Listing Icon">
    <span>Car Listing</span>
  </a>
  <a href="booked-cars.php">
    <img src="./image/nav-bar-icon/booking-icon.png" alt="Booking Icon">
    <span>Booked Car</span>
  </a>
  <a href="#">
    <img src="./image/nav-bar-icon/settings-icon.png" alt="Settings Icon">
    <span>Settings</span>
  </a>
</div>

<!-- Topbar -->
<div class="topbar d-flex justify-content-end">
  <div class="account-info" id="accountInfo">
    <img src="./image/nav-bar-icon/account-icon.png" width="30" alt="Account">
    <span>Account</span>
  </div>

  <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" style="width: 170px; top: 65px; right: 50px; display: none;" id="dropdownMenu">
    <a href="owner-profile.php" class="text-decoration-none text-dark">
      <div class="d-flex align-items-center mb-2 px-2 py-2 m-2 hover-effect">
        <img src="./image/nav-bar-icon/profile-icon.png" width="20" height="20" class="me-2">
        <span>Profile</span>
      </div>
    </a>
    <a href="../index.php" class="text-decoration-none text-dark">
      <div class="d-flex align-items-center px-2 py-2 m-2 hover-effect">
        <img src="./image/nav-bar-icon/logout-icon.png" width="20" height="20" class="me-2">
        <span>Logout</span>
      </div>
    </a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  <h4><?php echo htmlspecialchars($branch['branch_name']); ?> - Fees</h4>

  <!-- Back + Add Button -->
     <div class="lading-page-content">
  <div class="d-flex justify-content-between mb-3">
    <a href="owner-profile.php">
      <img src="./image/back-button.png" style="width: 20px; height: 20px;">
    </a>
    <button class="btn btn-warning text-white" id="addFeeBtn">Add Fee</button>
  </div>

  <!-- Fee List -->
  <?php foreach ($fees as $fee): ?>
    <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
      <div>
        <strong><?php echo htmlspecialchars($fee['fee_name']); ?></strong><br>
        ₱<?php echo htmlspecialchars($fee['fee_amount']); ?>
      </div>
      <div class="d-flex gap-2">
        <!-- Edit -->
        <button class="btn btn-light p-1" onclick='editFee(<?php echo json_encode($fee); ?>)'>
          <img src="./image/nav-bar-icon/edit-icon.png" width="25" height="25">
        </button>

        <!-- Delete -->
        <form action="../fees_process/delete_fee.php" method="POST" onsubmit="return confirm('Delete this fee?')">
          <input type="hidden" name="fee_id" value="<?php echo $fee['fee_id']; ?>">
          <button class="btn btn-light p-1">
            <img src="./image/nav-bar-icon/delete-icon.png" width="25" height="25">
          </button>
        </form>

      </div>
    </div>




  <form id="feeForm" method="POST" action="../fees_process/update_fee.php" style="display: none;" class="mt-3">
  <input type="hidden" name="fee_id" id="feeId">
  <input type="number" name="fee_amount" id="feeAmount" class="form-control" placeholder="Amount (₱)" required>
  <button type="submit" class="btn btn-primary">Save</button>
  <button type="button" class="btn btn-secondary ms-2" onclick="cancelFeeForm()">Cancel</button>
</form>

 <?php endforeach; ?>

 <!-- Hidden Add Fee Form -->
<div id="addFeeForm" style="display: none;" class="mt-3">
  <form method="POST" action="../fees_process/add_fee.php">
    <input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
    <input type="text" name="fee_name" class="form-control mb-2" placeholder="Fee Name" required>
    <input type="number" name="fee_amount" class="form-control mb-2" placeholder="Amount (₱)" required>
    <button type="submit" class="btn btn-success">Add Fee</button>
    <button type="button" class="btn btn-secondary ms-2" onclick="cancelAddFeeForm()">Cancel</button>
  </form>
</div>

</div>
  </div>
<script>
  const dropdownMenu = document.getElementById('dropdownMenu');
  const accountInfo = document.getElementById('accountInfo');




  accountInfo.addEventListener('click', function(e) {
    e.stopPropagation();
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', function(e) {
    if (!dropdownMenu.contains(e.target)) dropdownMenu.style.display = 'none';
  });

document.getElementById('addFeeBtn').addEventListener('click', () => {
  document.getElementById('addFeeForm').style.display = 'block';
  document.getElementById('feeForm').style.display = 'none'; // Hide edit form if open
}); 

function editFee(fee) {
  document.getElementById('feeForm').style.display = 'block';
  document.getElementById('feeId').value = fee.fee_id;
  document.getElementById('feeAmount').value = fee.fee_amount;
  document.getElementById('feeName').style.display = 'none'; // Hide fee name
  document.getElementById('feeForm').scrollIntoView({ behavior: 'smooth' });
}


function cancelAddFeeForm() {
  document.getElementById('addFeeForm').style.display = 'none';
}

function cancelFeeForm() {
  document.getElementById('feeForm').style.display = 'none'; // Hide the edit fee form
}


    

const form = document.getElementById('feeForm');

form.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
    if (data === 'success') {
        alert("Fee updated successfully!");
        document.getElementById('feeForm').style.display = 'none';

        // Optionally reload to see updated fee values
        location.reload(); // Optional if you want to refresh the list
    } else {
        alert(data); // Show the error message from PHP
    }
})

    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while updating the fee.");
    });
});


</script>

</body>
</html>
