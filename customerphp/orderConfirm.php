<?php
include('connect.php');
session_start();

date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_GET['rentID']) || !isset($_GET['payID'])) {
    die("Error: rentID and payID are required.");
}

if (!isset($_SESSION['custID']) || !isset($_SESSION['custName'])) {
    header("Location: loginPage.php");
    exit();
}


$custID = $_SESSION['custID'];
$custName = $_SESSION['custName'];
$stmt = $connect->prepare("SELECT custImage, custPhone, custEmail FROM customer WHERE custID = ?");
$stmt->bind_param("s", $custID);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($user) {
    $_SESSION['custPhone'] = $user['custPhone'];
    $_SESSION['custEmail'] = $user['custEmail'];
    $custImage = !empty($user['custImage']) ? $user['custImage'] : '../images/default-avatar.jpg';
} else {
    // Handle the case when no user is found
    die("Error: No customer found with the provided custID.");
}

$rentID = $_GET['rentID'];
$stmt = $connect->prepare("SELECT rentDepo, rentTotal, rentDetails FROM rental WHERE rentID = ?");
$stmt->bind_param("s", $rentID);
$stmt->execute();
$rentalData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rentalData) {
    die("Error: Rental data not found.");
}

$orderDetails = json_decode($rentalData['rentDetails'], true);
$subtotal = 0;
$rentDepo = $rentalData['rentDepo'] ?? 0; // Default to 0 if not set
$rentTotal = $rentalData['rentTotal'] ?? 0;

// Retrieve payment details
$payID = $_GET['payID'];
$stmt = $connect->prepare("SELECT payID, payMethod, payType, payBalance, payTotal FROM payment WHERE custID = ? AND payID = ?");
$stmt->bind_param("ss", $custID, $payID);
$stmt->execute();
$paymentData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$paymentData) {
    die("Error: Payment data not found.");
}

$payMethod = $paymentData['payMethod'] ?? 'Unknown';
$payType = $paymentData['payType'] ?? 'Unknown';
$payTotal = $paymentData['payTotal'] ?? 0;
$payBalance = $paymentData['payBalance'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <header class="text-center py-4">
        <h1>Shah Alam Camera</h1>
        <hr style="color: #eee">
    </header>

    <main class="container">
        <section class="text-center confirmation-details py-4">
            <img src="../images/Right tick.png" style="margin-bottom: 10px;width:100px; height:100px;"
                class="carousel-image mx-auto">
            <p style="font-size:1.5rem">Hye <?php echo htmlspecialchars($custID); ?>, Thank you for your order!</p>
            <div class="order-no">Order No: #<?php echo htmlspecialchars($rentID); ?></div>
        </section>

        <section class="order-summary bg-white p-4 shadow-lg rounded">
            <div class="row" style="font-size:1.1rem">
                <div class="col-md-4">
                    <p>Date: <strong><?php echo date("j F, Y"); ?></strong></p>
                    <p>Time: <strong><?php echo date("h.i A"); ?></strong></p>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4 text-end">
                    <p>Payment Method: <strong><?php echo htmlspecialchars($payMethod); ?></strong></p>
                    <p>Payment Type: <strong><?php echo htmlspecialchars($payType); ?></strong></p>
                </div>
            </div>

            <br>
            <h3>Order Summary</h3>
            <table class="table table-hover table-responsive">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Rental Period</th>
                        <th>Quantity</th>
                        <th>Total (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($orderDetails as $order) {
                        $prodName = $order['prodBrand'] . ' ' . $order['prodModel'];
                        $prodImage = $order['prodImage'];
                        $prodPrice = $order['prodPrice'];
                        $quantity = $order['quantity'];
                        $days = $order['days'];
                        $dateIn = $order['dateIn'];
                        $dateOut = $order['dateOut'];
                        $subtotal += $prodPrice * $days * $quantity;
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><img src="<?php echo htmlspecialchars($prodImage); ?>" alt="Product Image" class="img-fluid"
                                    style="max-width: 100px;"></td>
                            <td><?php echo htmlspecialchars($prodName); ?></td>
                            <td><?php echo date("j M, Y", strtotime($dateIn)) . " - " . date("j M, Y", strtotime($dateOut)); ?>
                            </td>
                            <td><?php echo $quantity; ?></td>
                            <td>RM <?php echo number_format($prodPrice * $days * $quantity, 2); ?></td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>

            <div class="row" style="font-size:1.1rem;">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 text-start">
                    <p><strong>Total Amount Paid: <span class="total-paid">RM
                                <?php echo number_format($payTotal, 2); ?></strong></span></p>
                    <hr>
                </div>
            </div>
        </section>
        <div>
            <a href="custHome.php" class="d-flex justify-content-center">
                <button style="text-decoration:none; margin: 50px 0;" class="btn btn-warning btn-lg w-50">Go to
                    Homepage</button>
            </a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #333;
            margin: 0;
            padding: 0;
            color: #eee;
        }

        h1 {
            color: #eee;

        }

        hr,
        h2,
        h3,
        p {
            font-weight: 600;
            color: #333;
        }

        .container {
            font-size: 1rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .order-no {
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            display: inline-block;
            background-color: #f0ad4e;
            color: #fff;
        }

        .table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            background-color: gray;
            color: white;
            font-weight: 600;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f7f7f7;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .table td img {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
        }

        .total-paid {
            color: #28a745;
        }

        .btn-warning {
            background-color: #f0ad4e;
            border: none;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            background-color: #ec971f;
        }

        footer {
            font-size: 0.9rem;
        }
    </style>
</body>

</html>