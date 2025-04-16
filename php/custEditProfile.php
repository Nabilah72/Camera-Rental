<?php
include('connect.php');
session_start();

if (!isset($_SESSION['custID'])) {
    header("Location: /login.php");
    exit();
}

$stmt = $connect->prepare("SELECT custImage, custID, custName, custPhone, custEmail FROM customer WHERE custID = ?");
$stmt->bind_param("s", $_SESSION['custID']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (empty($user['custImage'])) {
    $custImage = '../images/default-avatar.jpg';
} else {
    $custImage = $user['custImage'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $custName = trim($_POST['custName']);
    $custPhone = trim($_POST['custPhone']);
    $custEmail = trim($_POST['custEmail']);
    $currentImage = $_POST['currentImage'];

    if (empty($currentImage)) {
        $defaultImagePath = '../images/default-avatar.jpg';
        $removeImage = $connect->prepare("UPDATE customer SET custImage = ? WHERE custID = ?");
        $removeImage->bind_param("ss", $defaultImagePath, $_SESSION['custID']);
        $removeImage->execute();
        $removeImage->close();
    }
    if (!empty($_FILES['profilePic']['name'])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/ISP550/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES['profilePic']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetPath)) {
            $relativePath = "/ISP550/uploads/" . $fileName;
            $updateImage = $connect->prepare("UPDATE customer SET custImage = ? WHERE custID = ?");
            $updateImage->bind_param("ss", $relativePath, $_SESSION['custID']);
            $updateImage->execute();
            $updateImage->close();
        } else {
            echo "<script>alert('Failed to upload image. Please try again.');</script>";
        }
    }
    $update = $connect->prepare("UPDATE customer SET custName = ?, custPhone = ?, custEmail = ? WHERE custID = ?");
    $update->bind_param("ssss", $custName, $custPhone, $custEmail, $_SESSION['custID']);
    $update->execute();
    $update->close();

    header("Location: custProfile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.5/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <img width="50" height="50" src="../images/logo.png" alt="Company Logo">
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
                        <a class="nav-link" href="viewhistory.php">View History</a>
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
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="form-container">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <a href="custProfile.php" class="position-absolute top-0 end-0 p-2 text-dark"
                            style="text-decoration: none;">
                            <i class="bi bi-x-lg fs-4"></i>
                        </a>
                        <br><br>
                        <form method="POST" action="custEditProfile.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <div class="profile-container">
                                    <?php if (!empty($user['custImage'])): ?>
                                        <img id="imagePreview" src="<?php echo htmlspecialchars($user['custImage']); ?>"
                                            alt="Profile Picture" class="profile-picture">
                                    <?php else: ?>
                                        <img src="../images/default-avatar.jpg" alt="Default Avatar">
                                    <?php endif; ?>

                                </div>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="profilePic" name="profilePic"
                                        accept="image/*" onchange="previewImage();">
                                    <button type="button" class="btn btn-danger btn-sm remove-btn"
                                        onclick="removeProfilePicture()">Remove</button>
                                </div>
                                <input type="hidden" id="currentImage" name="currentImage"
                                    value="<?php echo htmlspecialchars($user['custImage']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="custID" class="form-label">Username</label>
                                <input type="text" class="form-control" id="custID" name="custID"
                                    value="<?php echo htmlspecialchars($user['custID']); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="custName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="custName" name="custName"
                                    value="<?php echo htmlspecialchars($user['custName']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="custPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="custPhone" name="custPhone"
                                    value="<?php echo htmlspecialchars($user['custPhone']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="custEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="custEmail" name="custEmail"
                                    value="<?php echo htmlspecialchars($user['custEmail']); ?>" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg">Save Changes</button>
                            </div>
                            <div class="text-center mt-4">
                                <hr>
                                <div class="d-flex justify-content-between px-3">
                                    <a href="changePass.php" class="text-decoration-none"
                                        style="color: #007BFF; font-size:1.2rem">Change
                                        Password</a>
                                    <a href="javascript:void(0);" onclick="confirmLogout()" class="text-decoration-none"
                                        style="color: red; font-size:1.2rem">Log Out</a>
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
                                                    window.location.href = "logout.php"; // Change this to your actual logout path
                                                }
                                            });
                                        }
                                    </script>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.5/dist/sweetalert2.min.js"></script>

    <script>
        function previewImage() {
            const fileInput = document.getElementById('profilePic');
            const imagePreview = document.getElementById('imagePreview');
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        function removeProfilePicture() {
            const imagePreview = document.getElementById('imagePreview');
            const fileInput = document.getElementById('profilePic');
            const currentImageInput = document.getElementById('currentImage');
            fileInput.value = '';
            currentImageInput.value = '';
            imagePreview.src = '../images/default-avatar.jpg';
        }

        document.querySelector("form").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent form submission until confirmation

            Swal.fire({
                title: 'Are you sure you want to save the changes?',
                text: "You are about to update your profile details.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save changes',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                backdrop: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    event.target.submit();
                }
            });
        });

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

        body {
            font-family: 'Montserrat', 'sans-serif';
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #333;
        }

        .form-container {
            max-width: 65%;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: black;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }

        .profile-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .file-input {
            display: none;
        }

        .btn-outline-dark {
            cursor: pointer;
            font-size: 14px;
        }

        .remove-btn {
            white-space: nowrap;
            font-size: 14px;
            border-radius: 8px;
        }

        .profile {
            width: 50%;
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
    </style>
</body>

</html>