<?php
include('connect.php');
session_start();

if (!isset($_SESSION['custID'])) {
    header("Location: login.php");
    exit();
}

$custID = $_SESSION['custID'];

$query = "SELECT custImage, custID, custName, custPhone, custEmail FROM customer WHERE custID = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $custID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
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
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.5/dist/sweetalert2.min.css" rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="custHome.php">
                <img width="50" height="50" src="../images/logo.png" alt="Company Logo">
            </a>
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

    <div class="profile-container position-relative">
        <a href="custHome.php" class="position-absolute top-0 end-0 p-2 text-dark" style="text-decoration: none;">
            <i class="bi bi-x-lg fs-4"></i>
        </a>
        <div class="d-flex align-items-center mb-3">
            <div class="profile-picture me-3">
                <?php if (!empty($user['custImage']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['custImage'])): ?>
                    <img src="<?php echo htmlspecialchars($user['custImage']); ?>?v=<?php echo time(); ?>"
                        alt="Profile Picture">
                <?php else: ?>
                    <img src="../images/default-avatar.jpg" alt="Default Avatar">
                <?php endif; ?>
            </div>
            <h5 class="mb-0"><?php echo htmlspecialchars($user['custID']); ?></h5>
        </div>

        <div class="mb-4">
            <strong>Name:</strong>
            <input type="text" class="form-control" placeholder="Name"
                value="<?php echo htmlspecialchars($user['custName']); ?>" disabled>
        </div>
        <div class="mb-4">
            <strong>Phone:</strong>
            <input type="tel" class="form-control" placeholder="Tel"
                value="<?php echo htmlspecialchars($user['custPhone']); ?>" disabled>
        </div>
        <div class="mb-4">
            <strong>Email:</strong>
            <input type="email" class="form-control" placeholder="Email"
                value="<?php echo htmlspecialchars($user['custEmail']); ?>" disabled>
        </div>
        <a href="custEditProfile.php" class="btn btn-warning btn-lg w-100">Edit Profile</a>

        <div class="text-center mt-4">
            <hr>
            <div class="d-flex justify-content-between px-3">
                <a href="changePass.php" class="text-decoration-none" style="color: #007BFF;font-size:1.2rem">Change
                    Password</a>
                <a href="javascript:void(0);" onclick="confirmLogout()" class="text-decoration-none"
                    style="color: red; font-size:1.2rem">Log Out</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.5/dist/sweetalert2.min.js"></script>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to log out of your account.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                backdrop: true // Ensures no interference with the container
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "logout.php";
                }
            });
        }
    </script>
    <style>
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

        .container {
            margin-top: 100px;
        }

        h1 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        body {
            background-color: #333;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            min-width: 700px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .profile-picture {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-picture input {
            position: absolute;
            bottom: 0;
            right: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
    </style>
</body>

</html>