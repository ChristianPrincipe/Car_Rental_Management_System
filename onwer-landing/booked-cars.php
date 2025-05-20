<?php

session_start();

require '../includes/db.php';

$owner_id = $_SESSION['owner_id'];

// fetching the booked cars 
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
        ORDER BY rentals.booking_date DESC";
        
$stmt = $pdo->prepare($sql);
$stmt->execute([$owner_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <a href="#">
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
        <h4>Booked Car</h4>

        <!-- lading-page-content" -->
        <div class="lading-page-content p-2 pt-3">
          <div class="nav-button d-flex flex-wrap justify-content-between align-items-center mb-3 px-3">
            <!-- Filter buttons --> 
            <div class="d-flex flex-wrap gap-2">
              <button class="btn bg-white text-black fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="filterByStatus('All')">All Bookings</button>
              <button class="btn bg-white text-warning fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="filterByStatus('Pending')">Pending Approval</button>
              <button class="btn bg-white text-success fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="filterByStatus('Approved')">Approved</button>
              <button class="btn bg-white text-danger fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="filterByStatus('Rejected')">Rejected</button>
              <button class="btn bg-white text-primary fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="filterByStatus('Completed')">Completed</button>
              <button class="btn bg-white text-secondary fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="filterByStatus('Cancelled')">Cancelled</button>
            </div>
          
          <div class="d-flex align-items-center gap-2">
          <!-- Download button -->
          <form method="POST" action="../report_process/book_report.php" class="m-0">
            <button class="btn bg-white text-black fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;">
              Download report
            </button>
          </form>

          <!-- Sort button -->
          <button class="btn bg-white text-black fw-normal btn-fill-hover border px-2 py-1" style="font-size: 11px;" onclick="sortTable()">
            Sort by Relevance
          </button>
        </div>

          
          </div>
          
          <hr>

          <!-- Bookings -->
          <div class="container">
            <div class="table-responsive">
              <table class="table table-bordered text-center align-middle small-table">
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
            </div>
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
    event.stopPropagation();
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
  });

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

  function filterByStatus(status) {
    const rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
      const statusSpan = row.querySelector(".status-badge");
      const currentStatus = statusSpan?.textContent.trim();

      if (status === "All" || currentStatus === status) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    const statusBadges = document.querySelectorAll(".status-badge");

    statusBadges.forEach(badge => {
      const status = badge.textContent.trim().toLowerCase();
      badge.classList.remove("bg-warning", "bg-success", "bg-primary", "bg-danger", "bg-secondary", "bg-dark");

      if (status === "approved") {
        badge.classList.add("bg-success");
      } else if (status === "pending") {
        badge.classList.add("bg-warning");
      } else if (status === "rejected") {
        badge.classList.add("bg-danger");
      } else if (status === "completed") {
        badge.classList.add("bg-primary");
      } else if (status === "cancelled") {
        badge.classList.add("bg-secondary");
      } else {
        badge.classList.add("bg-dark");
      }
    });
  });

  let originalRows = [];
  let isSorted = false;

  document.addEventListener("DOMContentLoaded", function () {
    const tbody = document.querySelector("table tbody");
    originalRows = Array.from(tbody.querySelectorAll("tr"));
  });

  function sortTable() {
    const tbody = document.querySelector("table tbody");

    if (!isSorted) {
      const statusOrder = ["Pending Approval", "Approved", "In Progress", "Completed", "Cancelled"];
      const rows = Array.from(tbody.querySelectorAll("tr"));

      rows.sort((a, b) => {
        const statusA = a.querySelector(".status-badge")?.textContent.trim();
        const statusB = b.querySelector(".status-badge")?.textContent.trim();
        const indexA = statusOrder.indexOf(statusA);
        const indexB = statusOrder.indexOf(statusB);
        return indexA - indexB;
      });

      tbody.innerHTML = "";
      rows.forEach(row => tbody.appendChild(row));
      isSorted = true;
    } else {
      tbody.innerHTML = "";
      originalRows.forEach(row => tbody.appendChild(row));
      isSorted = false;
    }
  }

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

  .btn-fill-hover {
    position: relative;
    overflow: hidden;
    transition: color 0.3s ease;
    z-index: 0;
  }

  .btn-fill-hover::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 0;
    background-color: currentColor;
    opacity: 0.2;
    transition: height 0.3s ease;
    z-index: -1;
  }

  .btn-fill-hover:hover::before {
    height: 100%;
  }

  .small-table td,
.small-table th {
  padding: 0.3rem !important; /* optional for tighter spacing */
  font-size: 12px;
}

</style>