<?php
session_start();
require '../includes/db.php';

// Get branch_id for the logged-in owner
$ownerId = $_SESSION['owner_id'];

// Get branch_id associated with the owner
$stmt = $pdo->prepare("SELECT branch_id, branch_name FROM branches WHERE owner_id = ?");
$stmt->execute([$ownerId]);
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

// If no branch is found for this owner, redirect or show an error
if (!$branch) {
    $_SESSION['error'] = "Branch not found for this owner.";
    header("Location: ../owner-landing/view-rules.php");
    exit();
}

// Get the branch_id
$branch_id = $branch['branch_id'];

// Select rules that belong to the logged-in owner's branch
$rsql = "SELECT rule_id, rule_name FROM rules WHERE branch_id = ?";
$stmt = $pdo->prepare($rsql);
$stmt->execute([$branch_id]);
$crules = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <h4><?php echo htmlspecialchars($branch['branch_name']); ?> Rules</h4>

  <!-- lading-page-content" -->
  <div class="lading-page-content">
    <div class="d-flex justify-content-between">
      <!-- Back button -->
      <a id="backButton" style="cursor: pointer; display: inline-block;">
        <img src="./image/back-button.png" alt="Back" style="width: 20px; height: 20px;">
      </a>

      <!-- New button (Add your button here) -->
       
      <button type="button" id="addRuleButton" style="background-color:rgb(217, 185, 41); color: white; padding: 6px 15px; border: none; border-radius: 5px; font-size: 14px;">
        Add Rule
      </button>

    </div>
    
    <!-- Editable form: hidden by default -->
    <form action="../owner_process/update_rules.php" method="POST" id="editForm" style="display: none;">
      <textarea name="rulesInput" placeholder="Enter Rules" oninput="autoResize(this)" 
      style="width: 100%; min-height: 100px; padding: 10px; font-size: 16px; margin-bottom: 10px; 
      overflow: hidden; resize: none; border: 1px solid black; margin-top: 10px;"></textarea>
    
      <!-- Hidden input for rule ID -->
      <input type="hidden" name="rule_id" id="ruleId" />

      <!-- Update button -->
      <button type="submit" style="background-color: #007bff; color: white; padding: 4px 12px; border: none; border-radius: 5px;">
        Update
      </button>
    
      <!-- Cancel button to hide the form -->
      <button type="button" onclick="toggleEditForm()" style="background-color: #ccc; color: black; padding: 4px 12px; border: none; border-radius: 5px; margin-left: 10px;">
        Cancel
      </button>
    </form>
    
    <!-- Add Rule Form (Initially Hidden) -->
    <form id="addRuleForm" style="display: none; margin-top: 10px;">
      <textarea id="newRuleInput" name="rule_name" placeholder="Enter new rule" oninput="autoResize(this)" 
      style="width: 100%; min-height: 100px; padding: 10px; font-size: 16px; margin-bottom: 10px; overflow: hidden; resize: none; border: 1px solid black;"></textarea>

      <input type="hidden" name="branch_id" id="branchId" value="<?php echo htmlspecialchars($branch_id); ?>">

      <!-- Submit Button -->
      <button type="submit" style="background-color:rgb(229, 183, 68); color: white; padding: 4px 12px; border: none; border-radius: 5px;">
        Save
      </button>

      <!-- Cancel Button -->
      <button type="button" onclick="document.getElementById('addRuleForm').style.display='none'" 
      style="background-color: #ccc; color: black; padding: 4px 12px; border: none; border-radius: 5px; margin-left: 10px;">
        Cancel
      </button>
    </form>


    <hr>

    <?php foreach ($crules as $index => $rule): ?>
      <div style="display: flex; align-items: center; justify-content: space-between; border: 1px solid #ccc; border-radius: 8px; padding: 10px; margin-bottom: 10px;">
        <div>
          <strong>Rule <?php echo $index + 1; ?>:</strong><br>
          <?php echo htmlspecialchars($rule['rule_name']); ?>
        </div>
        <div style="display: flex; gap: 10px;">
          <button type="button" onclick='editRule(<?php echo $rule['rule_id']; ?>, <?php echo json_encode($rule['rule_name']); ?>)' style="border: none; padding: 5px; border-radius: 5px; width: 40px;">
            <img src="./image/nav-bar-icon/edit-icon.png" style="width: 25px; height: 25px;" alt="Edit">
          </button>

          <form action="../owner_process/delete_rule.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this rule?');">
            <input type="hidden" name="rule_id" value="<?php echo $rule['rule_id']; ?>">
            <button type="submit" style="border: none; padding: 5px; border-radius: 5px; width: 40px;">
              <img src="./image/nav-bar-icon/delete-icon.png" style="width: 25px; height: 25px;" alt="Delete">
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
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

    // Close the dropdown when clicking outside of it
    document.addEventListener('click', function(event) {
      if (!accountInfo.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
      }
    });
  

    function toggleEditForm() {
      const form = document.getElementById('editForm');
      form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Function to auto-resize the textarea based on content
    function autoResize(textarea) {
      textarea.style.height = 'auto';
      textarea.style.height = textarea.scrollHeight + 'px';
    }

    // Function to fill the form with the selected rule data for editing
    function editRule(ruleId, ruleName) {
  document.getElementById('ruleId').value = ruleId;
  document.querySelector('[name="rulesInput"]').value = ruleName;
  toggleEditForm();

  // Smooth scroll to form
  document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
}


    // Parse the URL query string
    const urlParams = new URLSearchParams(window.location.search);
    const fromPage = urlParams.get('from');

    // Define the valid source pages and their destination URLs
    const validPages = {
      "owner-dashboard": "owner-dashboard.php",
      "owner-profile": "owner-profile.php"
    };

    // Fallback if 'from' is missing or invalid
    const fallbackPage = "owner-dashboard.php";

    // Set the actual link
    document.getElementById("backButton").href = validPages[fromPage] || fallbackPage;


  document.getElementById("addRuleButton").addEventListener("click", function () {
  document.getElementById("addRuleForm").style.display = "block";
  document.getElementById("newRuleInput").focus();
});

// Handle form submission
document.getElementById("addRuleForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const ruleName = document.getElementById("newRuleInput").value.trim();

  if (ruleName === "") {
    alert("Please enter a rule.");
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../owner_process/add_rule.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      alert("Rule added successfully!");
      window.location.reload();
    }
  };

  const branchId = document.getElementById("branchId").value;
xhr.send("rule_name=" + encodeURIComponent(ruleName) + 
"&branch_id=" + encodeURIComponent(branchId));
});


  </script>

  <!-- Optional JS -->
  <script src="../script.js"></script>
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
