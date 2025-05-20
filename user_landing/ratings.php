<?php

require '../includes/db.php';
session_start();

$customerId = $_SESSION['customer_id'];

$rsql = "SELECT 
    (
        SELECT carImages 
        FROM car_images 
        WHERE car_images.car_id = cars.car_id 
        LIMIT 1
    ) AS carImages,
    rentals.rental_id,
    cars.car_model,
    branches.branch_name
FROM rentals 
JOIN cars ON rentals.car_id = cars.car_id 
JOIN branches ON cars.branch_id = branches.branch_id
WHERE rentals.customer_id = ? 
AND rentals.reviewed = 0
AND rentals.bookingStatus_id = 5";  // Only completed bookings


$stmt = $pdo->prepare($rsql);
$stmt->execute([$customerId]); 
$rents = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Responsive Page</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="side-nav-bar-style/style-user.css">

<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="./image/nav-bar-icon/car-rental-logo.png" alt="Logo" class="logo">
  
  </div>
  
  

  <!-- Header -->
  <div class="topbar d-flex justify-content-between">

        <a href="ratings.php" class="d-flex flex-column align-items-center" style="text-decoration: none; color: #f0a500;">
            <img src="image/nav-bar-icon/notif-icon.png" width="20" height="20" alt="Account">
            <span style="font-size: 10px;">Notification</span>
        </a>
      

    <div class="account-info" id="accountInfo">
      <img src="./image/nav-bar-icon/account-icon.png" width="30" alt="Account">
      <span>Account</span>
    </div>

    <!-- Profile and Logout Section -->
  <div class="position-absolute bg-white text-dark rounded shadow p-2 m-1" style="width: 170px; top: 65px; right: 50px; display: none; background-color: #f0f0f0; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);" id="dropdownMenu">

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
    <h4>Notification</h4>

    <!-- lading-page-content" -->
    <div class="lading-page-content">

         <!-- back button -->
         <button onclick="history.back()" style="all: unset; cursor: pointer;">
            <img src="./image/rental-car-icon/back-button.png" alt="Back" style="width: 20px; height: 20px;">
        </button>

       <form id="ratingForm" action="../user_process/rating_validation.php" method="POST">
    <div class="container my-4">
         <?php if (count($rents) > 0): ?>
        <?php foreach($rents as $rent): ?>
            <div class="d-flex align-items-center border rounded p-3 bg-white shadow-sm flex-wrap">
                <input type="hidden" name="review[]" value="<?php echo htmlspecialchars($rent['rental_id']); ?>">

                <!-- Car Image -->
                <div class="me-5 d-flex align-items-center">
                    <img src="../uploads/<?php echo htmlspecialchars($rent['carImages']);?>" class="me-3 rounded" alt="Car" width="40" height="40">
                    <div><?php echo htmlspecialchars($rent['car_model']) ?></div>
                </div>

                <!-- Car & Shop Info -->
                <div class="me-4">
                    <h6><?php echo htmlspecialchars($rent['branch_name']) ?></h6>
                </div>

                <!-- Interactive Rating -->
                <div class="me-5 d-flex flex-column align-items-start ms-4">
                    <small class="text-muted"><strong>Rate Car</strong></small>
                    
                    <!-- Star Rating Group -->
                    <div class="star-rating" id="ratingGroup_<?php echo $rent['rental_id']; ?>">
                        <?php
                        $rentalId = $rent['rental_id']; // optional if unique IDs are needed
                        $ratingName = 'rating_' . $rentalId; // to make name unique per rental

                        for ($i = 5; $i >= 1; $i--) {
                            $starId = "star{$i}_{$rentalId}";
                            echo '<input type="radio" id="' . $starId . '" name="' . $ratingName . '" value="' . $i . '">';
                            echo '<label for="' . $starId . '">★</label>';
                        }
                        ?>
                    </div>

                    <!-- Error Message -->
                    <div id="ratingError_<?php echo $rent['rental_id']; ?>" class="text-danger small d-none">Please select a rating.</div>
                </div>

                <!-- Comment + Submit -->
                <div class="d-flex flex-grow-1 align-items-end">
                    <div class="me-2 w-100">
                        <label for="comment_<?php echo $rent['rental_id']; ?>" class="form-label mb-0 small">Add Comment</label>
                        <textarea class="form-control form-control-sm" id="comment_<?php echo $rent['rental_id']; ?>" name="comment[<?php echo $rent['rental_id']; ?>]" rows="1" placeholder="Write a comment..." style="resize: none; overflow: hidden;" oninput="autoExpand(this)"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-warning btn-sm mt-4">Submit</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-center text-muted mt-4">No review or ratings available yet.</p>
    <?php endif; ?>
    </div>
</form>





    </div>
     <!-- lading-page-content end" -->

  </div>


  <!-- Jquery File -->
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

  <script>

// <!-- jQuery Script -->
//     $(document).ready(function () {
//     $('#ratingForm').on('submit', function (e) {
//         e.preventDefault();

//         const rating = $('input[name="rating"]:checked').val();
//         if (!rating) {
//         $('#ratingError').removeClass('d-none');
//         return;
//         } else {
//         $('#ratingError').addClass('d-none');
//         }

//         // Submit form
//         $.ajax({
//         url: 'submit_rating.php',
//         type: 'POST',
//         data: $(this).serialize(),
//         success: function (response) {
//             alert('Thank you for your feedback!');
//             console.log(response);
//         },
//         error: function () {
//             alert('Error submitting your rating.');
//         }
//         });
//     });
//     });


// Exapnd text input
  
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ratingForm');
    
    form.addEventListener('submit', function (e) {
        // Get all rating groups for this form
        const ratingGroups = form.querySelectorAll('.star-rating');
        let valid = true;

        ratingGroups.forEach(group => {
            const rentalId = group.getAttribute('id').split('_')[1]; // Extract rental_id from the id
            const ratingRadio = document.querySelector(`input[name="rating_${rentalId}"]:checked`); // Updated to match the dynamic name
            const errorDiv = document.getElementById(`ratingError_${rentalId}`); // Target the specific error div

            if (!ratingRadio) {
                // Show the error message for the car that has no rating
                valid = false;
                if (errorDiv) {
                    errorDiv.classList.remove('d-none');
                }
            } else {
                if (errorDiv) {
                    errorDiv.classList.add('d-none');
                }
            }
        });

        // If valid, proceed to submit; otherwise, prevent form submission
        if (!valid) {
            e.preventDefault(); // Prevent form submission if any rating is missing
        }
    });
});



function autoExpand(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
  }

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



    .star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-start;
  }

  .star-rating input[type="radio"] {
    display: none;
  }

  .star-rating label {
    font-size: 24px;
    color: #ccc;
    cursor: pointer;
  }

  .star-rating input:checked ~ label,
  .star-rating label:hover,
  .star-rating label:hover ~ label {
    color: gold;
  }

  .text-danger {
    margin-top: 5px;
  }


</style>


