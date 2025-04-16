<?php
session_start();
include "connection.php";
if (!isset($_SESSION['adminName'])) {
    header("Location: login.php");
    exit();
}

$adminID = htmlspecialchars($_SESSION['adminID']);
$adminName = htmlspecialchars($_SESSION['adminName']);
$adminPhone = htmlspecialchars($_SESSION['adminPhone']);
$adminEmail = htmlspecialchars($_SESSION['adminEmail']);
$adminStatus = htmlspecialchars($_SESSION['adminStatus']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    body {
        background-color: #f2f2f2;
        padding-bottom: 50px;
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

    .status-active {
        color: green;
        font-weight: bold;
    }

    .status-inactive {
        color: red;
        font-weight: bold;
    }
</style>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="ms-2">Camera Rental Shah Alam</span>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                    id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../images/harraz.png" alt="Admin Profile" width="40" height="40" class="rounded-circle">
                    <span class="ms-2"><?= htmlspecialchars($_SESSION['adminName']); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown"
                    style="min-width: 200px;">
                    <li><a class="dropdown-item d-flex align-items-center" href="profile.php"><i
                                class="bi bi-person me-2"></i> View Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"
                            onclick="logoutConfirmation()"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <br>
    <div class="container">
        <div class="card">
            <div class="card-header text-center bg-dark text-white">
                <h3>Admin Profile</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="../images/harraz.png" alt="Admin Profile" class="rounded-circle" width="120" height="120">
                </div>
                <h5 class="text-center"><?= $adminName; ?></h5>
                <table class="table mt-4">
                    <tr>
                        <th scope="row">ID:</th>
                        <td><?= $adminID; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Name:</th>
                        <td><?= $adminName; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Phone Number:</th>
                        <td><?= $adminPhone; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Email:</th>
                        <td><?= $adminEmail; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Status:</th>
                        <td>
                            <?php if ($adminStatus == 'Active'): ?>
                                <span class="status-active"><?= $adminStatus; ?></span>
                            <?php else: ?>
                                <span class="status-inactive"><?= $adminStatus; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <div class="text-center mt-3">
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editAdminModal"
                        data-admin-id="<?= $adminID; ?>" data-admin-name="<?= $adminName; ?>"
                        data-admin-phone="<?= $adminPhone; ?>" data-admin-email="<?= $adminEmail; ?>"
                        data-admin-status="<?= $adminStatus; ?>">
                        Edit
                    </button>
                    <a href="javascript:void(0);" class="btn btn-danger" onclick="logoutConfirmation()">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            &copy; 2024 Camera Rental System. All Rights Reserved.
        </div>
    </footer>

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Admin Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="profileCRUD.php">
                    <input type="hidden" id="editAdminID" name="adminID">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" class="form-control" id="editAdminName" name="adminName" disabled>
                        </div>
                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="text" class="form-control" id="editAdminPhone" name="adminPhone" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" class="form-control" id="editAdminEmail" name="adminEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select id="editAdminStatus" name="adminStatus" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" name="editAdmin">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="jquery-3.7.1.min.js"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const editAdminModal = document.getElementById('editAdminModal');

        editAdminModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const adminID = button.getAttribute('data-admin-id');
            const adminName = button.getAttribute('data-admin-name');
            const adminPhone = button.getAttribute('data-admin-phone');
            const adminEmail = button.getAttribute('data-admin-email');
            const adminStatus = button.getAttribute('data-admin-status');

            editAdminModal.querySelector('#editAdminID').value = adminID;
            editAdminModal.querySelector('#editAdminName').value = adminName;
            editAdminModal.querySelector('#editAdminPhone').value = adminPhone;
            editAdminModal.querySelector('#editAdminEmail').value = adminEmail;
            editAdminModal.querySelector('#editAdminStatus').value = adminStatus;
        });

        // Display success message
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['success']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        // Display error message
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= $_SESSION['error']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

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