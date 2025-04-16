<?php
session_start();
include "connection.php";

// Pagination settings
$limit = 10; // Number of rows per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit;

// Fetch total records for calculating total pages
$totalRecordsQuery = "SELECT COUNT(*) as total FROM rental";
$totalRecordsResult = $connect->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch paginated data
$sql = "SELECT * FROM rental LIMIT $limit OFFSET $offset";
$result = $connect->query($sql);

if (!$result) {
    die("Invalid query: " . $connect->error);
}

$adminID = isset($_SESSION['adminID']) ? $_SESSION['adminID'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body {
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
        }

        .footer {
            position: relative;
            border-top: 1px solid #ddd;
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
        }

        .pagination-container {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="material-symbols-sharp me-2" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions"
                    style="font-size: 2rem; color: white;">menu</i>
                <img src="../images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="ms-2">Camera Rental Shah Alam</span>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                    id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../images/harraz.png" alt="Admin Profile" width="40" height="40" class="rounded-circle">
                    <span class="ms-2">
                        <?php if (isset($_SESSION['adminName'])): ?>
                            <?= htmlspecialchars($_SESSION['adminName']); ?>
                        <?php else: ?>
                            Guest
                        <?php endif; ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown"
                    style="min-width: 200px;">
                    <?php if (isset($_SESSION['adminName'])): ?>
                        <li><a class="dropdown-item d-flex align-items-center" href="profile.php">
                                <i class="bi bi-person me-2"></i> View Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"
                                onclick="logoutConfirmation()">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item d-flex align-items-center" href="login.php">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="../images/logo.png" alt="Logo" width="30" height="24"
                        class="d-inline-block align-text-top">
                    <span class="ms-2" style="color: black;">Camera Rental Shah Alam</span>
                </a>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="admin.php"
                role="button">
                <span class="material-symbols-sharp me-2">shield_person</span>
                <span>Admin</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="dashboard.php" role="button">
                <span class="material-symbols-sharp me-2">dashboard</span>
                <span>Dashboard</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="customer.php" role="button">
                <span class="material-symbols-sharp me-2">people</span>
                <span>Customer</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="payment.php" role="button">
                <span class="material-symbols-sharp me-2">payments</span>
                <span>Payment</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="product.php" role="button">
                <span class="material-symbols-sharp me-2">photo_camera</span>
                <span>Product</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="rental.php" role="button">
                <span class="material-symbols-sharp me-2">history</span>
                <span>Rental</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="shelff.php" role="button">
                <span class="material-symbols-sharp me-2">inventory_2</span>
                <span>Shelf</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="staff.php" role="button">
                <span class="material-symbols-sharp me-2">badge</span>
                <span>Staff</span>
            </a>
            <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                href="supplier.php" role="button">
                <span class="material-symbols-sharp me-2">inventory</span>
                <span>Supplier</span>
            </a>
        </div>
    </div>
    </nav>

    <!-- Edit Rental Modal -->
    <div class="modal fade" id="editRentalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Rental Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="rentCRUD.php">
                    <!-- Hidden Input for Rental ID -->
                    <input type="hidden" id="editRentID" name="rentID">

                    <!-- Hidden Input for Admin ID -->
                    <input type="hidden" id="editAdminID" name="adminID" value="<?= $adminID; ?>">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status:</label>
                            <select id="editRentStatus" name="rentStatus" class="form-select">
                                <option value="Cancelled">Cancelled</option>
                                <option value="Returned">Returned</option>
                                <option value="Not Returned">Not Returned</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" name="editRental">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table and Pagination -->
    <div class="container">
        <br>
        <h1>Rental Records</h1>
        <hr>
        <table class="table table-bordered mx-auto mt-3" style="width: 95%;">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">Rent ID</th>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Admin ID</th>
                    <th scope="col">Details</th>
                    <th scope="col">Deposit (RM)</th>
                    <th scope="col">Total (RM)</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["rentID"]; ?></td>
                        <td><?= $row["custID"]; ?></td>
                        <td><?= $row["adminID"]; ?></td>
                        <td><?= $row["rentDetails"]; ?></td>
                        <td><?= $row["rentDepo"]; ?></td>
                        <td><?= $row["rentTotal"]; ?></td>
                        <td style="text-align: center; color: 
                            <?= $row['rentStatus'] === 'Returned' ? 'green' : ($row['rentStatus'] === 'Not Returned' ? 'orange' : 'red'); ?>; 
                            font-weight: bold;">
                            <?= $row["rentStatus"]; ?>
                        </td>


                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="#" class="text-primary editRentBtn me-1" data-id="<?= $row['rentID']; ?>"
                                    data-status="<?= $row['rentStatus']; ?>" data-bs-toggle="modal"
                                    data-bs-target="#editRentalModal">
                                    <i class="material-symbols-sharp"
                                        style="font-size: 1.5rem; vertical-align: middle;">edit</i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination-container">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?= $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 Camera Rental Shah Alam</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editRentBtns = document.querySelectorAll('.editRentBtn');
        editRentBtns.forEach(button => {
            button.addEventListener('click', function() {
                const rentID = this.getAttribute('data-id');
                const rentStatus = this.getAttribute('data-status');

                // Set the modal fields with the current values
                document.getElementById('editRentID').value = rentID;
                document.getElementById('editRentStatus').value = rentStatus;
            });
        });

        function logoutConfirmation() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = '../homepage.html';
            }
        }
    </script>
</body>

</html>