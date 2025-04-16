<?php
session_start();
include('connect.php');

// Ensure user is logged in
if (!isset($_SESSION['custID'])) {
    header('Location: login.php');
    exit;
}

// Handle the rental cancellation
if (isset($_POST['cancel']) && isset($_POST['rentID'])) {
    $rentID = $_POST['rentID'];
    $custID = $_SESSION['custID'];

    // Start a transaction to update both rental and payment statuses
    $connect->begin_transaction();

    try {
        // Update rental status to 'Cancelled'
        $stmt = $connect->prepare("UPDATE rental SET rentStatus = 'Cancelled' WHERE rentID = ? AND custID = ?");
        $stmt->bind_param("ss", $rentID, $custID);
        $stmt->execute();
        $stmt->close();

        // Update payment status to 'Cancelled'
        $stmt = $connect->prepare("UPDATE payment SET payStatus = 'Cancelled' WHERE rentID = ? AND custID = ?");
        $stmt->bind_param("ss", $rentID, $custID);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction if both updates succeed
        $connect->commit();

        $_SESSION['message'] = "Rental and payment statuses have been successfully cancelled.";
        header('Location: viewhistory.php');
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of any error
        $connect->rollback();
        $_SESSION['message'] = "Error: Unable to cancel the rental and payment.";
        header('Location: viewhistory.php');
        exit;
    }
}

// Fetch user data
$stmt = $connect->prepare("SELECT custID, custImage, custName, custPhone, custEmail FROM customer WHERE custID = ?");
$stmt->bind_param("s", $_SESSION['custID']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$custImage = empty($user['custImage']) ? '../images/default-avatar.jpg' : $user['custImage'];

// Fetch rental details
$stmt = $connect->prepare("SELECT rentID, rentDepo, rentTotal, rentDetails FROM rental WHERE custID = ?");
$stmt->bind_param("s", $_SESSION['custID']);
$stmt->execute();
$rentals_result = $stmt->get_result();
$rentals = $rentals_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle sort and status filters
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$sortDirection = $sortOrder === 'oldest' ? 'ASC' : 'DESC';

$rentStatus = isset($_GET['rentStatus']) ? $_GET['rentStatus'] : '';
$statusCondition = !empty($rentStatus) ? " AND rental.rentStatus = ?" : '';

// Fetch rental and payment details with status filter
$stmt = $connect->prepare("
    SELECT rental.rentID, rental.rentDepo, rental.rentTotal, rental.rentDetails, 
           rental.rentStatus, MAX(payment.payDate) AS payDate, 
           MAX(payment.payStatus) AS payStatus, MAX(payment.payMethod) AS payMethod, 
           MAX(payment.payType) AS payType, 
           SUM(payment.payTotal) AS payTotal, 
           SUM(payment.payBalance) AS payBalance
    FROM rental
    LEFT JOIN payment ON rental.rentID = payment.rentID
    WHERE rental.custID = ? $statusCondition
    GROUP BY rental.rentID
    ORDER BY rental.rentID $sortDirection
");
if (!empty($rentStatus)) {
    $stmt->bind_param("ss", $_SESSION['custID'], $rentStatus);
} else {
    $stmt->bind_param("s", $_SESSION['custID']);
}

$stmt->execute();
$rentals_result = $stmt->get_result();
$rentals = $rentals_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ensure rental data is fetched
if (empty($rentals)) {
    $_SESSION['message'] = "No rental records found.";
    header('Location: viewhistory.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental History</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <img width="70" height="70" src="../images/logo.png" alt="Company Logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav" style="text-align:center;">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="custHome.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="custProduct.php">Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="custCart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="viewhistory.php">View History</a></li>
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
    <div class="container mt-5 pt-5">
        <h2 class="d-flex justify-content-center">Rental History</h2>
        <form method="GET" class="row my-4">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortOrder); ?>">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($searchStatus); ?>">
            <input type="hidden" name="rentStatus" value="<?php echo htmlspecialchars($rentStatus); ?>">

            <div class="col-md-4">
                <select name="sort" class="form-select">
                    <option value="newest" <?php echo $sortOrder === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="oldest" <?php echo $sortOrder === 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="rentStatus" class="form-select">
                    <option value="" <?php echo $rentStatus === '' ? 'selected' : ''; ?>>All Rent Status</option>
                    <option value="Not Returned" <?php echo $rentStatus === 'Not Returned' ? 'selected' : ''; ?>>Not
                        Returned</option>
                    <option value="Returned" <?php echo $rentStatus === 'Returned' ? 'selected' : ''; ?>>Returned
                    </option>
                    <option value="Cancelled" <?php echo $rentStatus === 'Cancelled' ? 'selected' : ''; ?>>Cancelled
                    </option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-warning w-100">Apply Filters</button>
            </div>
        </form>
        <?php if ($rentals): ?>
            <?php
            foreach ($rentals as $rental):
                $orderDetails = json_decode($rental['rentDetails'], true);
                ?>
                <div class="d-flex align-items-center justify-content-between mt-5" style="font-size: 1.3rem; margin: 0;">
                    <div class="column-1">
                        <p STYLE="margin-bottom:0"><strong>Rental ID
                                #<?php echo htmlspecialchars($rental['rentID']); ?></strong></p>
                    </div>
                </div>
                <div class="card mt-2 mb-4 rounded-4 shadow-sm">
                    <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center p-3">
                        <div class="status-section">
                            <strong>Rental Status:</strong>
                            <span class="status-text"><?php echo htmlspecialchars($rental['rentStatus']); ?></span>
                        </div>
                        <button class="btn btn-warning btn-md" data-bs-toggle="modal"
                            data-bs-target="#orderDetailsModal-<?php echo htmlspecialchars($rental['rentID']); ?>">
                            View Order Details
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Rental Period</th>
                                    <th>Quantity</th>
                                    <th>Price (RM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($orderDetails as $item): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($item['prodImage']); ?>" alt="Product Image"
                                                class="img-fluid" width="80" height="80">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['prodBrand']); ?></strong><br>
                                            <?php echo htmlspecialchars($item['prodModel']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $dateIn = DateTime::createFromFormat('Y-m-d', $item['dateIn'])->format('d/m/y');
                                            $dateOut = DateTime::createFromFormat('Y-m-d', $item['dateOut'])->format('d/m/y');
                                            echo htmlspecialchars($dateIn) . ' - ' . htmlspecialchars(string: $dateOut);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td>RM
                                            <?php echo number_format($item['prodPrice'] * $item['days'] * $item['quantity'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal for Order Details -->
                <div class="modal fade" id="orderDetailsModal-<?php echo htmlspecialchars($rental['rentID']); ?>" tabindex="-1"
                    aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content rounded-4">
                            <!-- Modal Header with payment Information -->
                            <div class="modal-header" style="background-color:#eee;">
                                <div class="w-100">
                                    <h5 class="modal-title" id="orderDetailsModalLabel" style="font-weight:600;">Rental ID #
                                        <?php echo htmlspecialchars($rental['rentID']); ?>
                                    </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <!-- Order Summary Section -->
                                <div class="mt-3">
                                    <table class="table -striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Brand & Model</th>
                                                <th>Quantity</th>
                                                <th>Price/Day (RM)</th>
                                                <th>Days</th>
                                                <th>Subtotal (RM)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orderDetails as $item): ?>
                                                <tr>
                                                    <td><img src="<?php echo htmlspecialchars($item['prodImage']); ?>"
                                                            alt="Product Image" class="img-fluid" width="50"></td>
                                                    <td><?php echo htmlspecialchars($item['prodBrand']) . ' ' . htmlspecialchars($item['prodModel']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['prodPrice']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['days']); ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($item['prodPrice'] * $item['days'] * $item['quantity']); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr style="margin:30px 0;">

                                <!-- Totals Section -->
                                <div class="mb-3">
                                    <h5 class="d-flex justify-content-center text-black m-4">Payment Details</h5>
                                    <table class="table" style="border-bottom: 1px solid #ddd;">
                                        <!-- Added bottom border style -->
                                        <tr>
                                            <th class="text-start">Total Rent:</th>
                                            <td class="text-end">RM <?php echo htmlspecialchars($rental['rentTotal']); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-start">Total Amount Paid:</th>
                                            <td class="text-end">RM <?php echo htmlspecialchars($rental['payTotal']); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-start">Remaining Balance:</th>
                                            <td class="text-end">RM <?php echo htmlspecialchars($rental['payBalance']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Footer with Buttons -->
                            <div class="modal-footer d-flex justify-content-center">
                                <form id="cancelRentForm" method="POST" action="viewhistory.php"
                                    onsubmit="event.preventDefault(); confirmCancel();">
                                    <input type="hidden" name="rentID"
                                        value="<?php echo htmlspecialchars($rental['rentID']); ?>">
                                    <input type="hidden" name="cancel" value="1">
                                    <button type="submit" class="btn btn-danger" <?php echo ($rental['rentStatus'] === 'Cancelled') ? 'disabled' : ''; ?>>
                                        CANCEL RENT
                                    </button>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>No rental history found.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmCancel() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will burn your deposit.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Cancelled!',
                        text: "Your rental has been successfully cancelled.",
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Submit the form after showing the success message
                        document.getElementById('cancelRentForm').submit();
                    });
                } else {
                    // Action when the user chooses not to cancel
                    console.log("Cancel action was aborted.");
                }
            });
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
            margin: 50px;
            font-size: 2rem;
            font-weight: 600;
            line-height: 2rem;
            animation: fade-in 1s ease-out;
            text-transform: uppercase;
        }

        .btn-outline-light {
            border-color: #fff;
            color: #fff;
        }

        .btn-outline-light:hover {
            background-color: #ff8800;
            border-color: #ff8800;
            color: white;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .card-header {
            background-color: #333;
            border-radius: 12px 12px 0 0;
            color: white;
            font-size: 1.2rem;
        }

        .card-body {
            padding: 20px;
        }

        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            background-color: #333;
        }

        .table thead th {
            background-color: #fff;
            text-align: center;
        }

        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</body>

</html>