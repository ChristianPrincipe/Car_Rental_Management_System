<?php

require 'includes/db.php'; 

session_start();

$stmt = $pdo->prepare("
    SELECT 
        cars.car_id,
        cars.car_model, 
        cars.capacity,
        cars.price,
        car_images.carImages,
        transmissiontype.transmissionType_name,
        fueltype.fuelType_name,
        cartype.carType_name
    FROM cars 
    JOIN car_images ON car_images.car_id = cars.car_id
    JOIN transmissiontype ON transmissiontype.transmissionType_id = cars.transmissionType_id
    JOIN fueltype ON fueltype.fuelType_id = cars.fuelType_id
    JOIN cartype ON cartype.carType_id = cars.carType_id
    GROUP BY cars.car_id
    ORDER BY cars.car_id DESC
    LIMIT 6
");
    $stmt->execute();
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center flex-column">

    <header>
        <div class="top-nav-bar d-flex align-items-center justify-content-between">
            <div class=" semi-logo d-flex align-items-center">
                <img src="lading-page-image/icon-car-top.png" alt="">
                <h1>Car rental</h1>
            </div>

            <div class=" nav-list d-flex align-items-center">
                <ul class=" nav-links d-flex list-unstyled mb-0 me-4">
                    <li class="me-3"><a href="#">Home</a></li>
                    <li><a href="about_us.html">About Us</a></li>
                </ul>
                <a class="login-button-design" href="./Login-forms/choose-to-login.html">Login</a>
            </div>
        </div>

    </header>

    <main class="d-flex justify-content-center">
    
                        
        
        <div class="lading-page-content position-relative">

            <!-- Start -->
            <div class="position-relative">

            <!-- Indicator kuhaon ra kung batian mo -->
            <?php
            if(isset($_SESSION['success'])){
                echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
                unset($_SESSION['success']);
            }
            
            ?>

            
                <!-- Info -->
                <div class="landing-info d-flex">

                
                    <div class="text-container d-flex flex-column justify-content-center align-items-end">

                    
                    
                        <div class=" text">
                            <h1>Your Ultimate Travel Companion, <span style="color: #f0a500;;">Rent Now</span></h1>
                            <p>We Offer A Wide Range Of Rental Cars To Suits Your
                            Needs, Whether You’re Planning A weekend Getaway, A Business Trip.</p>
                            <button class="btn btn-warning" style="width: 100px;"><a href="login-forms/choose-to-login.html" style="text-decoration: none; color:white">Rent Car</a></button>
                        </div>
                    </div>
                    
                    <!-- That yellow car in landing page -->
                    <div class="landing-car-img d-flex flex-column align-items-end">
                        <img class="logo" src="lading-page-image/logo.png">
                        <img class="car" src="lading-page-image/landing-page-car.png">
                    </div>
                </div>

                <!-- that color greay shape back at the car -->
                <div class="shape position-absolute" style="z-index: -1; top:58%;">
                    <img src="lading-page-image/shape.png">
                </div>

            </div>
            <!-- End -->



            <!-- Latest inventory -->
            <div class="car-container d-flex flex-column align-items-center justify-content-center" >
                <div class="latest-cars d-flex flex-column align-items-center p-5">
                    <h1 style="font-size: 25px; font-weight: 900;">Latest <span style="color: #f0a500;" >Inventory</span></h1>
                    <p style="font-size: 13px;">experience The Future Of Automotive Innovation With Our Latest Car Model’s</p>

<!--////////////// PHP DIRI ////////////////////////-->
                    <div class="d-flex flex-wrap justify-content-center mb-5">
                        
                        <!-- start -->
                         
                <!-- for ratingsss -->
                <?php
                    if($cars){
                        foreach($cars as $car){
                            $carId = $car['car_id'];

                            // Get average rating for this car
                            $sql = "SELECT rating
                                    FROM review
                                    JOIN rentals ON review.rental_id = rentals.rental_id
                                    WHERE rentals.car_id = :car_id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(['car_id' => $carId]);
                            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            $averageRating = 0;
                            if (count($reviews) > 0) {
                                $totalRating = array_sum(array_column($reviews, 'rating'));
                                $averageRating = round($totalRating / count($reviews), 1);
                            }

                ?>
                    <div class="rental-car m-2 rounded-3 p-3">
                        <div class="car-image">
                            <img src="uploads/<?php echo htmlspecialchars($car['carImages']); ?>" alt="Car Image"> 
                        </div>   
                        
                        <h4><?php echo htmlspecialchars($car['car_model']); ?></h4>
                        <div class="ratings d-flex align-items-center" style="gap: 8px; padding: 5px;">
                        <h5><?php echo htmlspecialchars($car['carType_name']); ?></h5>
                        </div>
        
                       
                        <div class="ratings d-flex align-items-center" style="gap: 4px;">
                    <?php if ($averageRating > 0): ?>
                        <div class="text-warning" style="font-size: 16px;">
                            <?php
                                $filledStars = floor($averageRating);
                                $emptyStars = 5 - $filledStars;
                                echo str_repeat('★', $filledStars) . str_repeat('☆', $emptyStars);
                            ?>
                        </div>
                        <span class="badge bg-light text-dark border rounded-pill" style="font-size: 12px;">
                            <?php echo number_format($averageRating); ?>/5
                        </span>
                    <?php else: ?>
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <div class="text-muted" style="font-size: 16px;">☆☆☆☆☆</div>
                            <span class="badge bg-light text-dark border rounded-pill" style="font-size: 12px;">0/5</span>
                        </div>
                    <?php endif; ?>
                </div>   

                        <!-- First row -->
                        <div class="first-row d-flex justify-content-between w-100 p-0">
                            <h6 class="rental-icon">
                                <img src="rental-car-icon/seats-icon.png">
                                <p><?php echo htmlspecialchars($car['capacity']); ?> Seats</p>
                            </h6>
                            <h6 class="rental-icon">
                                <img src="rental-car-icon/transmission-icon.png">
                                <p><?php echo htmlspecialchars($car['transmissionType_name']); ?></p>
                            </h6>
                        </div>

                        <hr style="border: 1px solid black; width: 100%; margin: 0; margin-bottom: 10px;">

                        <!-- Second row -->
                        <div class="second-row d-flex justify-content-between w-100">
                            <h6 class="rental-icon">
                                <img src="rental-car-icon/gas-icon.png">
                                <p><?php echo htmlspecialchars($car['fuelType_name']); ?></p>
                            </h6>
                            <h6 class="rental-icon">
                                <img class="peso-icon" src="rental-car-icon/peso-icon.png">
                                <p><?php echo number_format($car['price'], 2); ?>/day</p>
                            </h6>
                        </div>

                        <div class="rent-now-button">
                            <button class="rent-now-button-design">
                                <img src="rental-car-icon/rent-now-icon.png">
                                <a href="login-forms/choose-to-login.html">Rent Now</a>
                            </button>
                        </div>
                    </div>
                               <?php
                        }
                    } else {
                        echo "<p>No cars available</p>";
                    }
                    ?>


                         <!-- end -->
                        
                    </div>


 <!--////////////////////////////////////////////////  -->

                    <div class="d-flex flex-column align-items-center position-relative" style="width: 100%;">
                        <button class="see-all-button"><a href="login-forms/choose-to-login.html">See All</a></button>

                        <div class="position-absolute" style="left: 87%; width: 40%;">
                            <img width="100%" src="lading-page-image/car-outside.png">
                        </div>
                    </div>  
                   
               </div>
            </div>
            <!-- Latest inventory end-->


            <!-- Why Choose us -->
            <div class="why-choose-us-container d-flex flex-column align-items-center justify-content-center" >
                <h1 style="font-size: 30px; font-weight: 900;">Why <span style="color: #f0a500;" >Choose Us</span></h1>

                <div class="why-choose-us d-flex flex-wrap justify-content-center align-items-center" >
                    <div class="why-choose-us-info d-flex">
                        <img src="lading-page-image/afford-icon.png" alt="">
                        <div class="text-info">
                            <h6>Affordable Rates with No Hidden Fees</h6>
                            <p>Transparent pricing and competitive rates ensure you get the best value-what you see is what you pay.</p>
                        </div>
                    </div>

                    <div class="why-choose-us-info d-flex">
                        <img src="lading-page-image/car-icon.png" alt="">
                        <div class="text-info">
                            <h6>Easy Online Booking & Instant Confirmation</h6>
                            <p>Reserve your car in minutes with our user-friendly platform and receive instant booking confirmation.</p>
                        </div>
                    </div>

                    <div class="why-choose-us-info d-flex">
                        <img src="lading-page-image/wide-selection-icon.png" alt="">
                       <div class="text-info">
                            <h6>Wide Selection of Vehicles</h6>
                            <p>From economy cars to luxury SUVs, we offer a diverse fleet to suit every need and budget.</p>
                       </div>
                    </div>

                    <div class="why-choose-us-info d-flex">
                        <img src="lading-page-image/safety-icon.png" alt="">
                        <div class="text-info">
                            <h6>Verified Owners & Secure Transactions</h6>
                            <p>We carefully verify all vehicle owners and provide a secure platform to ensure a safe, smooth, and trustworthy rental experience for both owners and renters.

                            </p>
                        </div>
                    </div>
                
                </div>
            
            </div>
            <!-- Why Choose us end-->
            
            <div class="d-flex justify-content-center align-items-center m-5">
                <div class="rounded-4  p-3 m-5" style="background-color: #212121; width: 60%; height: 17rem;">
                    <div class="get-stared-text ms-3">
                        <h2>Ready To Get Started?</h2>
                        <p>Book your ride in minutes and hit the road with confidence-simple, fast, and hassle-free.</p>
                    </div>
                    <div class="ms-4 d-flex position-relative align-items-center" style="gap: 30px;">
                        <button class="btn btn-warning">
                            <a href="login-forms/choose-to-login.html" style="text-decoration: none; color:white">Create Account</a>
                        </button>
                        <img src="lading-page-image/get-started-car.png" style="width: 70%;">
                    </div>
                </div>
            </div>

        </div>


    </main>

       <footer class="h-100">
        <div class= "d-flex justify-content-between w-100 p-4 h-100">
            <div>
                <div>
                    <h4 style="color: #f0a500;">JJC Car Rental</h4>
                    <h6 style="color: white;">Your Ultimate Travel Companion</h6>
                </div>
            </div>

            <div class="d-flex" style="gap: 120px; padding-right: 20px;">
                <div>
                    <h6 style="color: #f0a500;"> Social Media</h6>
                    <div class="d-flex flex-column gap-1">
                        <a href="https://www.facebook.com/profile.php?id=61575377713013" target="_blank"  style="font-size: 13px; text-decoration: none; color: white; gap: 5px;"><img    style="width: 20px; margin-right: 5px;" src="fimage/fb.png"><span>Facebook</span></a>
                        <a href="https://www.instagram.com/jjc_Car_Rental" target="_blank" style="font-size: 13px; text-decoration: none; color: white; gap: 5px;"><img    style="width: 20px; margin-right: 5px;" src="fimage/x.png"><span>X</span></a>
                        <a href="https://www.instagram.com/jjc_Car_Rental" target="_blank" style="font-size: 13px; text-decoration: none; color: white; gap: 5px;"><img    style="width: 20px; margin-right: 5px;" src="fimage/insta.png"><span>Instagram</span></a>
                        <a href="https://www.tiktok.com/@jjc_Car_Rental" target="_blank" style="font-size: 13px; text-decoration: none; color: white; gap: 5px;"><img    style="width: 20px; margin-right: 5px;" src="fimage/tiktok.png"><span>Tiktok</span></a>
                    </div>
                </div>

                <div>
                    <h6 style="color: #f0a500;">Contact Us</h6>
                    <div>
                       <div>
                            <img width="20px" src="fimage/mail.png"> 
                            <span style="font-size: 13px; color: white;">JJC_CAR_RENTAL@gmail.com</span>
                        </div>

                        <div>
                            <img width="20px" src="fimage/call.png"> 
                            <span style="font-size: 13px; color: white;">09786341529</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div style="width: 100%; display: flex; justify-content: center; background-color: #212121; border-top: solid 1px white; padding-top: 5px; padding-bottom: 0;">
            <p style="color: #f0a500; font-size: 11px;">© 2025 JJC Car Rental. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>



</html>
