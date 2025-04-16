<?php
include "connection.php"; // Ensure this file contains your database connection code
session_start();

// Fetch data from the database for analytics
$totalCustomers = $connect->query("SELECT COUNT(*) AS total FROM customer")->fetch_assoc()['total'];
$totalRentals = $connect->query("SELECT COUNT(*) AS total FROM rental")->fetch_assoc()['total'];
$totalProducts = $connect->query("SELECT COUNT(*) AS total FROM product")->fetch_assoc()['total'];
$totalRevenue = $connect->query("SELECT SUM(payTotal) AS total FROM payment")->fetch_assoc()['total'];

$paymentMethodQuery = $connect->query("SELECT payMethod, COUNT(*) AS methodCount FROM payment GROUP BY payMethod");
$paymentMethods = [];
$methodCounts = [];
while ($row = $paymentMethodQuery->fetch_assoc()) {
  $paymentMethods[] = $row['payMethod'];
  $methodCounts[] = $row['methodCount'];
}

// Fetch data for Revenue Over Time
$revenueQuery = $connect->query("SELECT payDate, SUM(payTotal) AS dailyRevenue FROM payment GROUP BY payDate ORDER BY payDate");
$dates = [];
$revenues = [];
while ($row = $revenueQuery->fetch_assoc()) {
  $dates[] = $row['payDate'];
  $revenues[] = $row['dailyRevenue'];
}

// Fetch rental data
$rentalQuery = $connect->query("SELECT rentStatus, COUNT(*) AS statusCount FROM rental GROUP BY rentStatus");
$rentalStatuses = [];
$statusCounts = [];
while ($row = $rentalQuery->fetch_assoc()) {
  $rentalStatuses[] = $row['rentStatus'];
  $statusCounts[] = $row['statusCount'];
}

$rentalRevenueQuery = $connect->query("SELECT SUM(rentTotal) AS totalRevenue FROM rental");
$totalRentalRevenue = $rentalRevenueQuery->fetch_assoc()['totalRevenue'];

$rentalDepositQuery = $connect->query("SELECT SUM(rentDepo) AS totalDeposit FROM rental");
$totalRentalDeposit = $rentalDepositQuery->fetch_assoc()['totalDeposit'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <style>
    body {
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
      padding-bottom: 50px;
    }

    .card {
      margin-bottom: 20px;
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .chart-container {
      margin: 0 auto;
      width: 100%;
      height: 300px;
      max-height: 300px;
    }

    .card {
      border-radius: 8px;
      border: 1px solid #ddd;
      background-color: #fff;
    }

    .card h3 {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
    }

    #revenueChart {
      max-height: 600px;
      /* Adjust this to make the chart taller */
    }

    .chart-container canvas {
      max-height: 600px;
      margin: 0 auto;
    }

    h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
      font-size: 1.5rem;
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .chart-container {
        max-height: 300px;
      }

      .card h3 {
        font-size: 1.2rem;
      }
    }

    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: #000;
      color: #fff;
      text-align: center;
      padding: 10px 0;
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
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown" style="min-width: 200px;">
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
            <img src="../images/logo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
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
        <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="rental.php"
          role="button">
          <span class="material-symbols-sharp me-2">history</span>
          <span>Rental</span>
        </a>
        <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="shelff.php"
          role="button">
          <span class="material-symbols-sharp me-2">inventory_2</span>
          <span>Shelf</span>
        </a>
        <br><a class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-start" href="staff.php"
          role="button">
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

  <!-- Main Content -->
  <div class="container mt-4">
    <h1>Welcome,
      <?php if (isset($_SESSION['adminName'])): ?>
        <?= htmlspecialchars($_SESSION['adminName']); ?>
      <?php else: ?>
        Admin
        <?php endif; ?>!
    </h1>
    <p>Monitor your business performance with the latest stats and insights.</p>
    <hr>

    <!-- Dashboard Cards -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card bg-primary text-white text-center p-4">
          <h4>Total Customers</h4>
          <h2><?= htmlspecialchars($totalCustomers); ?></h2>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card bg-success text-white text-center p-4">
          <h4>Total Rentals</h4>
          <h2><?= htmlspecialchars($totalRentals); ?></h2>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card bg-warning text-white text-center p-4">
          <h4>Total Products</h4>
          <h2><?= htmlspecialchars($totalProducts); ?></h2>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card bg-danger text-white text-center p-4">
          <h4>Total Revenue (RM)</h4>
          <h2><?= number_format($totalRevenue, 2); ?></h2>
        </div>
      </div>
    </div>

    <div class="container mt-5">
      <div class="row">
        <!-- Payment Method Distribution -->
        <div class="col-lg-5 col-md-12 mb-4">
          <div class="card p-3 shadow-sm">
            <h3 class="text-center">Payment Method Distribution</h3>
            <div class="chart-container">
              <canvas id="paymentMethodChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Revenue Over Time -->
        <div class="col-lg-7 col-md-12 mb-4">
          <div class="card p-3 shadow-sm">
            <h3 class="text-center">Revenue Over Time</h3>
            <div class="chart-container">
              <canvas id="revenueChart" width="1000" height="500"></canvas>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Rental Status Distribution and Total Revenue vs Deposit (next to each other) -->
          <div class="col-lg-7 col-md-12 mb-4">
            <div class="card p-3 shadow-sm">
              <h3 class="text-center">Total Revenue vs Deposit</h3>
              <div class="chart-container">
                <canvas id="revenueDepositChart" width="1000" height="500"></canvas>
              </div>
            </div>
          </div>

          <div class="col-lg-5 col-md-12 mb-4">
            <div class="card p-3 shadow-sm">
              <h3 class="text-center">Rental Status Distribution</h3>
              <div class="chart-container">
                <canvas id="rentalStatusChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        &copy; 2024 Camera Rental System. All Rights Reserved.
      </footer>

      <!-- Scripts -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        // Payment Method Distribution Chart
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(paymentMethodCtx, {
          type: 'pie',
          data: {
            labels: <?= json_encode($paymentMethods); ?>,
            datasets: [{
              data: <?= json_encode($methodCounts); ?>,
              backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
              borderColor: '#ffffff',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              }
            }
          }
        });

        // Revenue Over Time Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
          type: 'line',
          data: {
            labels: <?= json_encode($dates); ?>,
            datasets: [{
              label: 'Daily Revenue (RM)',
              data: <?= json_encode($revenues); ?>,
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 2,
              fill: true
            }]
          },
          options: {
            responsive: true,
            scales: {
              x: {
                title: {
                  display: true,
                  text: 'Date'
                }
              },
              y: {
                title: {
                  display: true,
                  text: 'Revenue (RM)'
                },
                beginAtZero: true
              }
            }
          }
        });

        // Rental Status Distribution Chart
        const rentalStatusCtx = document.getElementById('rentalStatusChart').getContext('2d');
        new Chart(rentalStatusCtx, {
          type: 'pie',
          data: {
            labels: <?= json_encode($rentalStatuses); ?>,
            datasets: [{
              data: <?= json_encode($statusCounts); ?>,
              backgroundColor: function(context) {
                let status = context.chart.data.labels[context.dataIndex];
                if (status === 'Not Returned') {
                  return '#FFA500'; // Orange for 'Not Returned'
                } else if (status === 'Cancelled') {
                  return '#ff0000'; // Red for 'Cancelled'
                } else if (status === 'Returned') {
                  return '#008000'; 
                }
                return ['#28a745', '#ffc107', '#28a745'][context.dataIndex % 3]; // Green, Yellow, Blue for others
              },
              borderColor: '#ffffff',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              }
            }
          }
        });

        // Revenue vs Deposit Chart
        const revenueDepositCtx = document.getElementById('revenueDepositChart').getContext('2d');
        new Chart(revenueDepositCtx, {
          type: 'bar',
          data: {
            labels: ['Total Revenue', 'Total Deposit'],
            datasets: [{
              label: 'Amount (RM)',
              data: [<?= $totalRentalRevenue ?>, <?= $totalRentalDeposit ?>],
              backgroundColor: ['#28a745', '#FFA500'], // Green for Total Revenue, Orange for Total Deposit
              borderColor: ['#28a745', '#FFA500'],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Amount (RM)'
                }
              }
            }
          }
        });
      </script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
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