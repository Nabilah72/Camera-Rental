<?php
session_start();
include "connection.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<style>
    body {
        background-color: #f2f2f2;
        padding-bottom: 50px;
    }

    html,
    body {
        height: 100%;
        margin: 0;
        overflow-x: hidden;
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        border-top: 1px solid #ddd;
        background-color: black;
        color: white;
        text-align: center;
        padding: 10px 0;
    }

    .container-fluid {
        max-width: 100%;
        padding: 0 15px;
        flex: 1;
    }
</style>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid d-flex align-items-center">

            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="material-symbols-sharp me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions" style="font-size: 2rem; color: white;">menu</i>

                <img src="../images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="ms-2">Camera Rental Shah Alam</span>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../images/harraz.png" alt="Admin Profile" width="40" height="40" class="rounded-circle">
                    <span class="ms-2">
                        <?php if (isset($_SESSION['adminName'])): ?>
                            <?= htmlspecialchars($_SESSION['adminName']); ?>
                        <?php else: ?>
                            Guest
                        <?php endif; ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown" style="min-width: 200px;">
                    <?php if (isset($_SESSION['adminName'])): ?>
                        <li><a class="dropdown-item d-flex align-items-center" href="profile.php">
                                <i class="bi bi-person me-2"></i> View Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" onclick="logoutConfirmation()">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item d-flex align-items-center" href="login.php">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        <img src="../images/logo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
                        <span class="ms-2" style="color: black;">Camera Rental Shah Alam</span>
                    </a>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="admin.php" role="button">
                    <span class="material-symbols-sharp me-2">shield_person</span>
                    <span>Admin</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="dashboard.php" role="button">
                    <span class="material-symbols-sharp me-2">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="customer.php" role="button">
                    <span class="material-symbols-sharp me-2">people</span>
                    <span>Customer</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="payment.php" role="button">
                    <span class="material-symbols-sharp me-2">payments</span>
                    <span>Payment</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="product.php" role="button">
                    <span class="material-symbols-sharp me-2">photo_camera</span>
                    <span>Product</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="rental.php" role="button">
                    <span class="material-symbols-sharp me-2">history</span>
                    <span>Rental</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="shelff.php" role="button">
                    <span class="material-symbols-sharp me-2">inventory_2</span>
                    <span>Shelf</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="staff.php" role="button">
                    <span class="material-symbols-sharp me-2">badge</span>
                    <span>Staff</span>
                </a>
                <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="supplier.php" role="button">
                    <span class="material-symbols-sharp me-2">inventory</span>
                    <span>Supplier</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Table in the center -->
    <div class="container" style="margin-bottom: 0.5px;">
        <br>
        <h1>Customer Records</h1>
        <hr>
        <table class="table table-bordered mx-auto mt-3" style="width: 95%;">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Phone Number</th>
                    <th scope="col">Email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include "connection.php";

                $sql = "SELECT * FROM customer";
                $result = $connect->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connect->error);
                }

                while ($row = $result->fetch_assoc()) {
                    $password = $row["custPassword"];
                    $censoredPassword = substr($password, 0, 1) . str_repeat('*', max(0, strlen($password) - 2)) . substr($password, -1);

                    echo "<tr>
                    <td>" . $row["custID"] . "</td>
                    <td>" . $row["custName"] . "</td>
                    <td>" . $row["custPhone"] . "</td>
                    <td>" . $row["custEmail"] . "</td>
                </tr>";
                }
                ?>
            </tbody>
        </table>
        <footer class="footer">
            <div class="container-fluid">
                &copy; 2024 Camera Rental System. All Rights Reserved.
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const sidebar = document.getElementById('sidebar');
            const hamburgerIcon = document.getElementById('hamburger-icon');

            hamburgerIcon.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });

            function logoutConfirmation() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to log out.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, log out!',
                    cancelButtonText: 'No, stay logged in.',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "../homepage.html";
                    }
                });
            }
        </script>
</body>

</html>