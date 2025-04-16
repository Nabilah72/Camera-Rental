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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>Home</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar fixed-top text-white" style="background-color: #807c78">
        <div class="container-fluid">
            <img width="70" height="70" src="../images/logo.png" alt="Company Logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav ">
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
            <span class="navbar-text d-none d-lg-block text-white">Welcome Back,
                <?php echo htmlspecialchars($_SESSION['custID']); ?>!
                <a href="custProfile.php">
                    <img style="cursor: pointer" src="<?php echo htmlspecialchars($custImage); ?>" alt="Profile Picture"
                        class="rounded-circle ms-2" width="40" height="40">
                </a>
            </span>
        </div>
    </nav>

    <section class="welcome-section">
        <div class="background-overlay">
            <img src="../images/bg-img.png" alt="Background Image" class="background-image">
        </div>
        <div class="text-overlay">
            <div class="welcome-content">
                <h1 class="fade-in">Shah Alam Camera Rentals</h1>
                <p class="fade-in delay-1">Your one-stop destination for premium camera equipment. <br>Rent hassle-free
                    and
                    capture unforgettable moments.</p>
            </div>
        </div>
    </section>


    <section id="products" class="py-5 slide-in" style="min-height:80vh; background-color:#eee">
        <div class="container">
            <div class="row text-center mb-4">
                <h2>Explore More</h2>
            </div>
            <div class="row">
                <div class="col-md-4 d-flex align-items-stretch" style="text-align: center;margin:20px auto;">
                    <div class="card mb-4 shadow-sm slide-in" style="height: 100%;">
                        <img src="../images/card1.jpg" class="card-img mx-auto" alt="Products">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Browse Products</h5>
                            <p class="card-text flex-grow-1" style="margin:10px 0;">
                                Explore our range of cameras and lenses for all your photography needs.
                            </p>
                            <a href="custProduct.php" class="btn btn-warning btn-lg w-100">Find out more</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-stretch" style="text-align: center; margin:20px auto;">
                    <div class="card mb-4 shadow-sm slide-in" style="height: 100%;">
                        <img src="../images/card2.jpg" class="card-img-top" alt="Cart">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">View Cart</h5>
                            <p class="card-text flex-grow-1" style="margin:10px 0;">
                                Check and manage the items in your cart before proceeding to checkout.
                            </p>
                            <a href="custCart.php" class="btn btn-warning btn-lg w-100">Find out more</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-stretch" style="text-align: center; margin:20px auto;">
                    <div class="card mb-4 shadow-sm slide-in" style="height: 100%;">
                        <img src="../images/card3.jpg" class="card-img-top" alt="Profile">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Manage Profile</h5>
                            <p class="card-text flex-grow-1" style="margin:10px 0;">
                                Update your profile information, preferences, and account details.
                            </p>
                            <a href="custProfile.php" class="btn btn-warning btn-lg w-100">Find out more</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class=" section py-5 slide-in" style="background-color: #eee; min-height:80vh">
        <div class="container">
            <h2 class="text-center mb-4" style="margin-bottom:10px;">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <!-- FAQ Item 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How do I rent a camera?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show bg-light" aria-labelledby="headingOne"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            To rent a camera, browse our catalog, select the desired product, and complete the checkout
                            process. You can either rent for a day or for longer periods.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            What payment methods are accepted?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse bg-light" aria-labelledby="headingTwo"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We accept various payment methods, including credit cards, debit cards, and cash.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Can I extend the rental period?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse bg-light" aria-labelledby="headingThree"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, you can extend the rental period. Please contact us before the original rental period
                            ends to arrange the extension.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Is there a late return fee?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse bg-light" aria-labelledby="headingFour"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, there is a late return fee. If the item is not returned by the due date, a fee will be
                            applied based on the duration of the delay.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            How do I cancel or modify my rental reservation?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse bg-light" aria-labelledby="headingFive"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can modify or cancel your reservation by logging into your account. If you need
                            assistance, you can also contact our customer support team.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 7 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            Do you deliver the rented equipment?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse bg-light" aria-labelledby="headingSix"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We do not provide any delivery services since the product was damaged in the previous
                            purchase. You can pick up and return the product at our physical store.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 8 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            How can I contact customer support?
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse  bg-light" aria-labelledby="headingEight"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can contact our customer support team via email, phone, or our live chat service on the
                            website.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <section id="contact" class="py-5 slide-in" style="background-color:#807c78;">
        <div class="container">
            <div class="contact-section text-center text-white">
                <h2>Contact Us Today!</h2>
                <br>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <img style="max-height: 270px; width: 100%; object-fit: cover;" src="../images/kedai.jpg"
                            alt="Shop Image">
                    </div>

                    <!-- Contact Information Section -->
                    <div class="col-md-8 text-start text-white">
                        <h4 class="mt-3">Shah Alam Camera</h4><br>
                        <p><strong>Tel:</strong> +60 18 874 8487</p>
                        <p><strong>Email:</strong> gadgetpro88@yahoo.com</p>
                        <p><strong>Address:</strong> No. 25A, Tingkat 1, Jalan Nelayan 19/A, 40300, Shah Alam, Selangor,
                            MY</p>

                        <div class="social-icons mt-3">
                            <a href="https://www.facebook.com/kedaicamera.shahalam/?locale=ms_MY" target="_blank">
                                <img class="icons" src="../images/fb.png" style="margin: 2px;" alt="Facebook">
                            </a>
                            <a href="https://www.instagram.com/explore/locations/606739204/kedai-camera-shah-alam/?hl=en"
                                target="_blank">
                                <img class="icons" src="../images/ig.webp" style="margin: 2px;" alt="Instagram">
                            </a>
                            <a href="https://www.tiktok.com/@shahalamcamera?lang=en" target="_blank">
                                <img class="icons" src="../images/tt.webp" style="margin: 2px;" alt="TikTok">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="text-center py-3 text-white fade-in" style="background-color: #807c78">
        <p>© Shah Alam Camera. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sections = document.querySelectorAll('.slide-in, .fade-in');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        sections.forEach(section => observer.observe(section));
    </script>
    <style>
        .dropdown-menu {
            display: none;
            justify-content: center;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .navbar,
        .navbar-nav .nav-link,
        .navbar-text {
            display: flex;
            justify-content: center;
            color: white !important;
        }

        .navbar-nav .nav-link:hover {
            color: #fdc50e !important;
        }

        .navbar {
            z-index: 1000;
            font-size: 1.2rem;
        }

        body {
            font-family: 'Montserrat', sans-serif;
        }

        .text-overlay {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.1));
            display: flex;
            align-items: center;
            padding: 0 5%;
        }

        .welcome-content {
            color: #fdc50e;
            position: relative;
            z-index: 1;
            max-width: 1000px;
        }

        .welcome-section {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        h1 {
            font-size: 4rem;
            font-weight: 600;
            line-height: 5rem;
            animation: fade-in 1s ease-out;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h2 {
            text-align: center;
            font-weight: 500;
            font-size: 2rem;

        }

        h5 {
            text-align: center;
            font-weight: 600;
            margin: 10px 0;
        }

        p {
            font-size: 1.2rem;
            line-height: 2rem;
            animation: fade-in 1.5s ease-out;
        }

        .background-image {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        section {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .contact-section {
            color: #000;
            text-align: center;
        }

        .icons {
            max-width: 30px;
            max-height: 30px;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .fade-in {
            opacity: 0;
            transition: opacity 1s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
        }

        .slide-in {
            transform: translateY(20%);
            opacity: 0;
            transition: transform 1s ease-out, opacity 1s ease-out;
        }

        .slide-in.visible {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</body>

</html>