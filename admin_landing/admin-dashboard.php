<?php
include '../includes/db.php'; 

session_start();

$adminId = $_SESSION['admin_id'];

$stmt = $pdo->prepare("SELECT 
  owner_id, 
  owner_name, 
  owner_emailAddress, 
  owner_businessPermit, 
  approval_status 
  FROM owners 
  WHERE approval_status = 'pending'");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  </div>
  
  <!-- Header -->
  <div class="topbar d-flex justify-content-end gap-4">
    <div class="account-info" id="accountInfo">
      <img src="../user_landing/image/nav-bar-icon/account-icon.png" width="30" alt="Account">
      <span>Account</span>
    </div>
    <!-- Profile and Logout Section -->
    <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">
      <a href="../index.php" class="text-decoration-none text-dark">
        <div class="d-flex align-items-center px-2 py-2 m-2 hover-effect">
          <img src="../user_landing/image/nav-bar-icon/logout-icon.png" alt="Logout" width="20" height="20" class="me-2">
          <span>Logout</span>
        </div>
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h4>Admin Dashboard</h4>
    <div class="lading-page-content">
      <table class="table table-bordered table-striped table-sm">
        <thead>
          <tr>
            <th class="text-center py-3">Rental Shop Name</th>
            <th class="text-center py-3">Owner Name</th>
            <th class="text-center py-3">Email</th>
            <th class="text-center py-3">Permit ID</th>
            <th class="text-center py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($result as $row): ?>
            <tr>
              <td class="text-center small py-3"><?= htmlspecialchars($row['owner_name']) ?></td>
              <td class="text-center small py-3"><?= htmlspecialchars($row['owner_name']) ?></td>
              <td class="text-center small py-3"><?= htmlspecialchars($row['owner_emailAddress']) ?></td>
              <td class="text-center small py-3"><?= htmlspecialchars($row['owner_businessPermit']) ?></td>
              <td class="text-center py-2">
                <form method="POST" action="owner_approval.php" class="d-inline">
                  <input type="hidden" name="owner_id" value="<?= $row['owner_id'] ?>">
                  <button class="btn btn-success btn-sm" name="action" value="approve">Approve</button>
                  <button class="btn btn-danger btn-sm" name="action" value="reject">Reject</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
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

      function changeImage(imageSrc) {
      // Change the main image source when side image is clicked
      document.getElementById("mainImage").src = imageSrc;
    }

    function approveAction(shopName) {
    alert(shopName + ' has been approved.');
    // Additional approval logic here
  }

  function cancelAction(shopName) {
    alert(shopName + ' has been canceled.');
    // Additional cancellation logic here
  }


</script>

  <!-- Optional JS -->
   <script src="script.js"></script>
   <script src="./jquery.js"></script>
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


  
  .highlighted{
      background-color: #212121 !important;
    }

    .step-icons {
      flex-wrap: wrap;
      gap: 10px;
      row-gap: 0.5rem; /* or use gap only for row spacing */
      column-gap: clamp(0.25rem, 2vw, 1.5rem); /* flexible horizontal spacing */
    }

    .step-icons img {
        width: clamp(30px, 5vw, 50px);   /* Shrinks the width responsively */
        height: clamp(30px, 5vw, 50px);  /* Shrinks the height responsively */
        border-radius: clamp(5px, 1vw, 10px);  /* Dynamic border-radius */
        padding: clamp(5px, 1vw, 10px);  /* Dynamic padding */
        background: #f0f0f0;
      }


    .step-item {
      position: relative;
      padding: 0 30px;
      text-align: center;
      font-size: 11px;
      font-weight: bold;
      padding: 0 clamp(5px, 1vw, 20px); 
      flex: 0 1 auto;
    }

    .step-item.active img{
      background-color: #212121;
    }




</style>