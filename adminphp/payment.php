<?php
session_start();
include "connection.php";

// Set the number of records per page
$recordsPerPage = 10;

// Get the current page number, default to 1 if not set
$pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($pageNumber - 1) * $recordsPerPage;

// Get the total number of records
$sqlTotal = "SELECT COUNT(*) AS total FROM payment";
$resultTotal = $connect->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalRecords = $rowTotal['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Query to fetch the payment records for the current page
$sql = "SELECT * FROM payment LIMIT $recordsPerPage OFFSET $offset";
$result = $connect->query($sql);

if (!$result) {
    die("Invalid query: " . $connect->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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

    .disabled {
        cursor: not-allowed;
        color: grey !important;
    }

    .disabled:hover::after {
        content: attr(title);
        position: absolute;
        background: #333;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        white-space: nowrap;
        font-size: 0.9rem;
    }

    .text-success {
        color: #28a745;
        font-weight: bold;
    }

    .text-warning {
        color: #fd7e14;
        font-weight: bold;
    }

    .text-danger {
        color: #dc3545;
        font-weight: bold;
        /* Makes text bold */
    }
</style>

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
                <a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start"
                    href="admin.php" role="button">
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

    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="paymentCRUD.php">
                    <!-- Hidden Input for Admin ID -->
                    <input type="hidden" id="editPayID" name="payID">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Total:</label>
                            <input type="number" class="form-control" id="editPayTotal" name="payTotal" disabled>
                        </div>
                        <div class="form-group">
                            <label>Remaining Balance:</label>
                            <input type="number" class="form-control" id="editRemainBal" name="remainBal" disabled>
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select id="editPayStatus" name="payStatus" class="form-select">
                                <option value="Cancelled">Cancelled</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" name="editPayment">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table in the center -->
    <div class="container" style="margin-bottom: 0.5px;">
        <br>
        <h1>Payment Records</h1>
        <hr>
        <table class="table table-bordered mx-auto mt-3" style="width: 95%;">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">Payment ID</th>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Time</th>
                    <th scope="col">Date</th>
                    <th scope="col">Type</th>
                    <th scope="col">Method</th>
                    <th scope="col">Total (RM)</th>
                    <th scope="col">Balance (RM)</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    // Assign the class based on the payStatus value
                    $payStatusClass = "";
                    if ($row["payStatus"] == "Completed") {
                        $payStatusClass = "text-success"; // Green for Completed
                    } elseif ($row["payStatus"] == "Pending") {
                        $payStatusClass = "text-warning"; // Orange for Pending
                    } elseif ($row["payStatus"] == "Cancelled") {
                        $payStatusClass = "text-danger";
                    }

                    echo "<tr>
                            <td>" . $row["payID"] . "</td>
                            <td>" . $row["custID"] . "</td>
                            <td>" . $row["payTime"] . "</td>
                            <td>" . $row["payDate"] . "</td>
                            <td>" . $row["payType"] . "</td>
                            <td>" . $row["payMethod"] . "</td>
                            <td>" . $row["payTotal"] . "</td>
                            <td>" . $row["payBalance"] . "</td>
                            <td class='text-center " . $payStatusClass . "'>" . $row["payStatus"] . "</td>
                            <td>
                                <div class='d-flex justify-content-center align-items-center'>
                                    <a href='#' class='text-primary editPaymentBtn me-1' data-id='{$row['payID']}'
                                        data-total='{$row['payTotal']}' data-balance='{$row['payBalance']}'
                                        data-status='{$row['payStatus']}' data-bs-toggle='modal'
                                        data-bs-target='#editPaymentModal'>
                                        <i class='material-symbols-sharp' style='font-size: 1.5rem; vertical-align: middle;'>edit</i>
                                    </a>
                                </div>
                            </td>
                        </tr>";
                }
                ?>
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
        <footer class="footer">
            <div class="container-fluid">
                &copy; 2024 Camera Rental System. All Rights Reserved.
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="jquery-3.7.1.min.js"></script>
        <script src="sweetalert2.all.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Display success message
            <?php if (isset($_SESSION['success_message'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= $_SESSION['success_message']; ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            // Display error message
            <?php if (isset($_SESSION['error_message'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= $_SESSION['error_message']; ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        </script>

        <script>
            // JavaScript to handle the "Edit Payment" modal
            document.addEventListener("DOMContentLoaded", () => {
                // Get all elements with the "editPaymentBtn" class
                const editPaymentButtons = document.querySelectorAll(".editPaymentBtn");

                // Loop through each button
                editPaymentButtons.forEach(button => {
                    button.addEventListener("click", () => {
                        // Retrieve the data attributes from the button
                        const payID = button.getAttribute("data-id");
                        const payTotal = button.getAttribute("data-total");
                        const remainBal = button.getAttribute("data-balance");
                        const payStatus = button.getAttribute("data-status");

                        // Set the values in the modal form
                        document.getElementById("editPayID").value = payID;
                        document.getElementById("editPayTotal").value = payTotal;
                        document.getElementById("editPayBalance").value = payBalance;
                        document.getElementById("editPayStatus").value = payStatus;
                    });
                });
            });

            document.addEventListener("DOMContentLoaded", () => {
                const editPaymentButtons = document.querySelectorAll(".editPaymentBtn");

                editPaymentButtons.forEach(button => {
                    const payStatus = button.getAttribute("data-status");

                    if (payStatus === "Completed") {
                        // Disable editing functionality
                        button.removeAttribute("data-bs-toggle");
                        button.removeAttribute("data-bs-target");

                        // // Add disabled class for styling
                        // button.classList.add("disabled");

                        // Add a click event listener to show SweetAlert2 for disabled buttons
                        button.addEventListener("click", (e) => {
                            e.preventDefault(); // Prevent default action
                            Swal.fire({
                                icon: 'warning',
                                title: 'Action Disabled',
                                text: 'Editing is not allowed for completed payments.',
                                confirmButtonText: 'OK'
                            });
                        });
                    } else {
                        button.addEventListener("click", () => {
                            // For enabled buttons, populate modal fields
                            const payID = button.getAttribute("data-id");
                            const payTotal = button.getAttribute("data-total");
                            const remainBal = button.getAttribute("data-balance");
                            const payStatus = button.getAttribute("data-status");

                            document.getElementById("editPayID").value = payID;
                            document.getElementById("editPayTotal").value = payTotal;
                            document.getElementById("editRemainBal").value = remainBal;
                            document.getElementById("editPayStatus").value = payStatus;
                        });
                    }
                });
            });
        </script>

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