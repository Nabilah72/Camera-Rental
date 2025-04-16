<?php
include('connect.php');
session_start();

if (!isset($_SESSION['custName'])) {
    header("Location: login.php");
    exit();
}

$stmt = $connect->prepare("SELECT custImage FROM customer WHERE custID = ?");
$stmt->bind_param("s", $_SESSION['custID']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (empty($user['custImage'])) {
    $custImage = '../images/default-avatar.jpg';
} else {
    $custImage = $user['custImage'];
}

if (isset($_GET['prodID'])) {
    $prodID = $_GET['prodID'];
    $stmt = $connect->prepare("SELECT prodImage, prodID, prodBrand, prodModel, prodPrice, currentQty FROM product WHERE prodID = ?");
    $stmt->bind_param("s", $prodID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    echo json_encode($result);
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: #eeee">
        <div class="container-fluid">
            <img width="70" height="70" src="../images/logo.png" alt="Company Logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav" style="text-align:center;">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="custHome.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="custProduct.php">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="custCart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="viewhistory.php">View History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="custProfile.php">Profile</a>
                    </li>
                </ul>
            </div>
            <span class="navbar-text d-none d-lg-block">Welcome Back,
                <?php echo htmlspecialchars($_SESSION['custID']); ?>!
                <a href="custProfile.php">
                    <img style="cursor: pointer" src="<?php echo htmlspecialchars($custImage); ?>" alt="Profile Picture"
                        class="rounded-circle ms-2" width="40" height="40">
                </a>
            </span>
        </div>
    </nav>

    <section id="cameras" class="section py-5"
        style="display: flex; align-items:center; min-height:120vh;background-color: #eee">
        <div class="container">
            <div class="text-center mb-4">
                <h2>Cameras</h2>
                <p class="lead">Explore our extensive collection of cameras, crafted to meet your needs. </p>
            </div>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100">
                        <div id="camera1Carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../images/EOS_front.png" class="d-block carousel-image mx-auto"
                                        alt="Camera Front View">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/EOS_back.png" class="d-block carousel-image mx-auto"
                                        alt="Camera Top View">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/EOS_top.png" class="d-block carousel-image mx-auto"
                                        alt="Camera Side View">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/EOS_left.png" class="d-block carousel-image mx-auto"
                                        alt="Camera Bottom View">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#camera1Carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#camera1Carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">CANON EOS R5</h5>
                            <p class="card-text">45MP Full-frame, 8K Video, Dual Pixel AF, In-body Stabilization, Wi-Fi
                            </p>
                        </div>
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('C001')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>
                <!-- card 2 -->
                <div class="col">
                    <div class="card h-100">
                        <div id="camera2Carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../images/alpa_front.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/alpa_back.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/alpha_top.webp" class="d-block carousel-image mx-auto">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#camera2Carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#camera2Carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">SONY ALPHA A7 III</h5>
                            <p class="card-text">24MP Full-frame, 4K Video, Fast Hybrid AF, Compact Design, 10fps Burst
                            </p>
                        </div>
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('C002')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col">
                    <div class="card h-100">
                        <div id="camera3Carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../images/nikon_front.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/nikon_back.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/nikon_top.webp" class="d-block carousel-image mx-auto">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#camera3Carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#camera3Carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">NIKON D3500</h5>
                            <p class="card-text">24MP APS-C, Beginner-Friendly, Lightweight, Full HD 1080p Video,
                                SnapBridge</p>
                        </div>
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('C003')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>

                <!-- card 4 -->
                <div class="col">
                    <div class="card h-100">
                        <div id="camera4Carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../images/zv_back.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/zv_both.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/zv_top.webp" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/zv_left.webp" class="d-block carousel-image mx-auto">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#camera4Carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#camera4Carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">SONY ZV-1 II</h5>
                            <p class="card-text">20MP 1-inch Sensor, 4K Video, Ideal for Vlogging, Built-in ND Filter,
                                Compact</p>
                        </div>
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('C004')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>

                <!-- card 5 -->
                <div class="col">
                    <div class="card h-100">
                        <div id="camera5Carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../images/g7_front.png" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/g7_front_slant.png" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/g7_back.png" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/g7_top.png" class="d-block carousel-image mx-auto">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#camera5Carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#camera5Carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">CANON PowerShot G7 X Mark III</h5>
                            <p class="card-text">20MP 1-inch Sensor, 4K Video, Flip-Up Screen, Ideal for Vlogs</p>
                        </div>
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('C005')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>
                <!-- card 6 -->
                <div class="col">
                    <div class="card h-100">
                        <div id="camera6Carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../images/EOS_90D_front.png" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/EOS_90D_BACK.png" class="d-block carousel-image mx-auto">
                                </div>
                                <div class="carousel-item">
                                    <img src="../images/eos_90D_TOP.png" class="d-block carousel-image mx-auto">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#camera6Carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#camera6Carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">CANON EOS 90D</h5>
                            <p class="card-text">32.5MP APS-C, 4K Video, High Burst Rate, Dual Pixel AF, Great Battery
                                Life
                            </p>
                        </div>
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('C006')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="lens" class="section bg-white py-5" style="display: flex; align-items:center; min-height:60vh;">
        <div class="container">
            <div class="text-center mb-4">
                <h2>Lens</h2>
                <p class="lead">Discover our diverse range of lenses, tailored for your creative vision.</p>
            </div>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <!-- Lens Card 1 -->
                <div class="col">
                    <div class="card h-100">
                        <img src="../images/canon_70.webp" class="carousel-image mx-auto">
                        <div class="card-body text-center">
                            <h5 class="card-title">CANON RF 24-70mm f/2.8L IS USM</h5>
                            <p class="card-text">Versatile Zoom, Image Stabilization, High-Speed Autofocus</p>
                        </div>
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('L001')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>

                <!-- Lens Card 2 -->
                <div class="col">
                    <div class="card h-100">
                        <img src="../images/sony.jpg" class="carousel-image mx-auto">
                        <div class="card-body text-center">
                            <h5 class="card-title">SONY FE 50mm f/1.8</h5>
                            <p class="card-text">Compact Prime Lens, Bright f/1.8 Aperture, Lightweight, Fast AF
                            <p>
                        </div>
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('L002')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>
                <!-- Lens Card 3 -->
                <div class="col">
                    <div class="card h-100">
                        <img src="../images/nikon.webp" class="carousel-image mx-auto">
                        <div class="card-body text-center">
                            <h5 class="card-title">NIKON AF-S 35mm f/1.8G</h5>
                            <p class="card-text">Prime Lens, Bright Aperture, Compact, Great for Portraits</p>
                        </div>
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('L003')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>
                <!-- Lens Card 4 -->
                <div class="col">
                    <div class="card h-100">
                        <img src="../images/canon_50.webp" class="carousel-image mx-auto">
                        <div class="card-body text-center">
                            <h5 class="card-title">CANON EF 50mm f/1.8 STM</h5>
                            <p class="card-text">Affordable Prime, Fast f/1.8, Great for Portraits, Smooth AF.</p>
                        </div>
                        <div class="card-footer bg-transparent text-center">
                            <button class="btn btn-warning btn-lg w-50" onclick="fetchProductData('L004')">Rent
                                Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="text-center py-3 text-dark fade-in" style="background-color: #eee; color: black;">
        <p>© Shah Alam Camera. All Rights Reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fetchProductData(prodID) {
            fetch(`custProduct.php?prodID=${prodID}`)
                .then(response => response.json())
                .then(data => showRentPopup(data.prodImage, data.prodID, data.prodBrand, data.prodModel, data.prodPrice, data.currentQty))
                .catch(error => console.error('Error fetching product data:', error));
        }

        function showRentPopup(prodImage, prodID, prodBrand, prodModel, prodPrice, currentQty) {
            const popupHTML = `
        <div id="rentPopup" class="popup-overlay">
            <div class="popup-content">
                <button class="close-popup" onclick="closePopup()">&times;</button>
                <div class="inside-box" style="display: flex; align-items: center;">
                    <img class="popup-image" src="${prodImage}" alt="Product Image">
                    <table class="popup-table">
                        <tr>
                            <th>Product ID :</th>
                            <td>${prodID}</td>
                        </tr>
                        <tr>
                            <th>Brand :</th>
                            <td>${prodBrand}</td>
                        </tr>
                        <tr>
                            <th>Model :</th>
                            <td>${prodModel}</td>
                        </tr>
                        <tr>
                            <th>Price/day:</th>
                            <td>RM ${prodPrice}</td>
                        </tr>
                    </table>
                </div>
                <hr>
                <p><strong>Details</strong>
                    <i class="bi bi-info-circle" style="cursor: pointer; float: right;" onclick="showRulesPopup()"> Read T&C</i>
                </p>
                <table class="table">
                    <tr>
                        <td><label for="dateIn" class="form-label">Pickup Date :</label></td>
                        <td><input id="dateIn" type="date" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><label for="dateOut" class="form-label">Return Date :</label></td>
                        <td><input id="dateOut" type="date" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><label for="quantity" class="form-label">Quantity :</label></td>
                        <td><input id="quantity" type="number" min="1" max="${currentQty}" value="1" class="form-control"></td>
                    </tr>
                </table>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-warning btn-lg w-50" onclick="addToCart('${prodImage}', '${prodID}', '${prodBrand}', '${prodModel}', ${prodPrice}, ${currentQty})">Add to Cart</button>
                </div>
            </div>
        </div>`;
            document.body.insertAdjacentHTML('beforeend', popupHTML);
        }

        function addToCart(prodImage, prodID, prodBrand, prodModel, prodPrice, currentQty) {
            const dateIn = document.getElementById('dateIn').value;
            const dateOut = document.getElementById('dateOut').value;
            const quantity = parseInt(document.getElementById('quantity').value, 10);

            if (!dateIn || !dateOut) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select both start and end dates.'
                });
                return;
            }

            const today = new Date();
            const oneMonthLater = new Date();
            oneMonthLater.setMonth(today.getMonth() + 1);

            const startDate = new Date(dateIn);
            const endDate = new Date(dateOut);

            if (startDate < today || startDate > oneMonthLater) {
                displayRulesPopup();
                return;
            }

            if (quantity > currentQty) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Selected quantity exceeds available stock.'
                });
                return;
            }

            if (endDate <= startDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Return date must be after the Pickup date.'
                });
                return;
            }

            const rentalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const params = new URLSearchParams({
                prodID,
                prodImage,
                prodBrand,
                prodModel,
                prodPrice,
                dateIn,
                dateOut,
                quantity,
                days: rentalDays,
                action: "add"
            });
            window.location.href = `custCart.php?${params.toString()}`;
        }

        function displayRulesPopup() {
            const rulesHTML = `
        <div id="rulesPopup" class="popup-overlay">
            <div class="popup-content">
                <button class="close-popup" onclick="closeRulesPopup()">&times;</button>
                <h4 style="text-align:center;">Rules & Regulations</h4>
                <p>Pickup dates must be within 1 month from today's date. Please choose a valid date.</p>
                <button class="btn btn-dark" onclick="closeRulesPopup()">Got It</button>
            </div>
        </div>`;
            document.body.insertAdjacentHTML('beforeend', rulesHTML);
        }

        function showRulesPopup() {
            const rulesHTML = `
        <div id="rulesPopup" class="popup-overlay">
            <div class="popup-content">
                <h4 style="margin-top: 10px; text-align:center;">Rules & Regulations</h4>
                <hr>
                <p style="margin-left:20px;"><Strong>General Rules</Strong></p>
                <ol>
                    <li><Strong>Pickup Date Policy: </strong>
                    <ul>
                        <li>The pickup date for any rental must be booked no more than one month from today's date.</li>
                        <li>Failure to adhere to this policy will result in the rental being denied.</li>
                    </ul>
                    <li><strong>Return Policy:</strong>
                    <ul>
                        <li>All rented products must be returned on or before the agreed return date.</li>
                        <li>Late returns will incur a penalty of RM 50 per day, per item.</li>
                    </ul>
                    <li><Strong>Product Condition:</Strong>
                    <ul>
                        <li>All equipment must be returned in the same condition as it was rented. </li>
                        <li>Customers will be held liable for any damage, loss, or theft of rented equipment.</li>
                    </ul>
                        <li><strong>Cancellations and Refunds:</strong>
                    <ul>
                        <li>Cancellations made at least 3 days before the pickup date will receive a full refund. </li>
                        <li>Cancellations made less than 3 days in advance are non-refundable.</li>
                    </ul>
                </ol>
                <br>
                <p style="margin-left:20px;"><Strong>Steps to Rent a Product</Strong></p>
                <ol>
                    <li>Choose your product and check availability.</li>
                    <li>Select a pickup date no later than <?php echo date("F j, Y", strtotime("+1 month")); ?>.</li>
                    <li>Choose a return date</li>
                    <li>Review the rental details, quantity, rental period, and total cost.</li>
                    <li>Complete the payment to secure your rental.</li>
                    <li>Collect your item on the pickup date.</li>
                    <li>Inspect the product before signing the rental agreement.</li>
                    <li>Handle the equipment with care.</li>
                    <li>Return the product on time to avoid late fees.</li>
                    <br><hr>
                    <p><strong>Note:</strong> By renting equipment from us, you agree to comply with these rules and regulations. Non-compliance may result in additional charges or termination of rental privileges.</p>
                </ol>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-dark btn-lg w-50" onclick="closeRulesPopup()">Got It</button>
                </div>
            </div>
        </div>`;
            document.body.insertAdjacentHTML('beforeend', rulesHTML);
        }

        function closeRulesPopup() {
            document.getElementById('rulesPopup').remove();
        }

        function closePopup() {
            document.getElementById('rentPopup').remove();
        }

    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .navbar-nav .nav-link:hover {
            color: #fdc50e !important;
        }

        .navbar {
            z-index: 1000;
            font-size: 1.2rem;
        }

        .dropdown-menu {
            display: none;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        h1,
        h2,
        h3,
        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 2rem;
            animation: fade-in 1s ease-out;
            text-transform: uppercase;
        }

        h5 {
            font-size: 1.3rem;
            font-weight: 600;
            animation: fade-in 1s ease-out;
            text-transform: uppercase;
        }

        p {
            font-size: 1.2rem;
            line-height: 2rem;
            animation: fade-in 1.5s ease-out;
        }

        .carousel-image {
            margin: 10px 0;
            max-width: 200px;
            max-height: 150px;
            object-fit: contain;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(100%) sepia(100%) saturate(100%) hue-rotate(0deg) brightness(0) contrast(100%);
        }

        .col {
            margin: 30px auto;
        }

        .popup-table th {
            width: 130px;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-image {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 4px;
            margin: 10px 30px;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 600px;
            max-height: 80vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .close-popup {
            text-align: right;
            font-size: 30px;
            color: black;
            background: transparent;
            border: none;
            cursor: pointer;
            position: relative;
        }

        table tr,
        table td,
        p {
            font-size: 1.1rem;
        }
    </style>
</body>

</html>