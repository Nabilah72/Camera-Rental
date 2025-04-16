<?php
session_start();
include "connection.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>
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

  .text-success {
    color: green;
    font-weight: bold;
  }

  .text-danger {
    color: red;
    font-weight: bold;
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

  <!-- Edit Admin Modal -->
  <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Admin Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="adminCRUD.php">
          <!-- Hidden Input for Admin ID -->
          <input type="hidden" id="editAdminID" name="adminID">
          <div class="modal-body">
            <!-- <div class="form-group">
              <label>Profile Picture:</label>
              <input type="file" class="form-control" id="editAdminProfilePicture" name="profilePicture" accept="image/*">
            </div> -->
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
              <label>Password:</label>
              <input type="password" class="form-control" id="editAdminPassword" name="adminPassword" required>
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

  <!-- Table in the center -->
  <div class="container" style="margin-bottom: 0.5px;">
    <br>
    <h1>Admin Records</h1>
    <hr>
    <table class="table table-bordered mx-auto mt-3" style="width: 95%;">
      <thead class="thead-dark text-center">
        <tr>
          <th scope="col">Admin ID</th>
          <th scope="col">Name</th>
          <th scope="col">Phone Number</th>
          <th scope="col">Email</th>
          <th scope="col">Status</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php

        include "connection.php";

        $sql = "SELECT * FROM admin";
        $result = $connect->query($sql);

        if (!$result) {
          die("Invalid query: " . $connect->error);
        }

        while ($row = $result->fetch_assoc()) {
          $password = $row["adminPassword"];
          $censoredPassword = substr($password, 0, 1) . str_repeat('*', max(0, strlen($password) - 2)) . substr($password, -1);

          echo "<tr>
                <td>{$row['adminID']}</td>
                <td>{$row['adminName']}</td>
                <td>{$row['adminPhone']}</td>
                <td>{$row['adminEmail']}</td>
                <td>
                <div class='d-flex justify-content-center align-items-center'>";
          $statusClass = ($row['adminStatus'] === 'Active') ? 'text-success' : 'text-danger';
          echo "<span class='$statusClass'>{$row['adminStatus']}</span>";
          echo "</div>
                </td>
                <td>
                    <div class='d-flex justify-content-center align-items-center'>
                    <a href='#' class='text-primary editAdminBtn me-1' data-id='{$row['adminID']}'
                                                                        data-name='{$row['adminName']}'
                                                                        data-phone='{$row['adminPhone']}'
                                                                        data-email='{$row['adminEmail']}'
                                                                        data-password='{$row['adminPassword']}'
                                                                        data-status='{$row['adminStatus']}'
                                                                        data-bs-toggle='modal'
                                                                        data-bs-target='#editAdminModal'>
                        <i class='material-symbols-sharp' style='font-size: 1.5rem; vertical-align: middle;'>edit</i>
                    </a>
                </td>
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
    <script src="jquery-3.7.1.min.js"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Populate Edit Admin Modal
      document.querySelectorAll('.editAdminBtn').forEach(button => {
        button.addEventListener('click', () => {
          // Populate modal fields with admin data from the clicked button
          document.getElementById('editAdminID').value = button.dataset.id;
          document.getElementById('editAdminName').value = button.dataset.name;
          document.getElementById('editAdminPhone').value = button.dataset.phone;
          document.getElementById('editAdminEmail').value = button.dataset.email;
          document.getElementById('editAdminPassword').value = button.dataset.password; // You might want to hash or hide this in production
          document.getElementById('editAdminStatus').value = button.dataset.status;
        });
      });
      // Logout Confirmation
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