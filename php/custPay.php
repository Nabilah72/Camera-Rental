<?php
include('connect.php');
session_start();

// Set timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check session variables
if (!isset($_SESSION['custID']) || !isset($_SESSION['custName'])) {
    header("Location: login.php");
    exit();
}

$custID = $_SESSION['custID'];
$custName = $_SESSION['custName'];

// Fetch customer profile image
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
    die("Error: No customer found with the provided custID.");
}

// Retrieve or set rentID from URL or session
if (isset($_GET['rentID'])) {
    $rentID = $_GET['rentID'];
    $_SESSION['rentID'] = $rentID;
} elseif (isset($_SESSION['rentID'])) {
    $rentID = $_SESSION['rentID'];
} else {
    die("Error: rentID not provided in the URL or session.");
}

// Fetch rental details
$query = $connect->prepare("SELECT rentDepo, rentTotal FROM rental WHERE rentID = ? AND custID = ?");
$query->bind_param("ss", $rentID, $custID);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $rentalData = $result->fetch_assoc();
    $rentDepo = (float) $rentalData['rentDepo'];
    $rentTotal = (float) $rentalData['rentTotal'];
} else {
    die("Error: No rental record found for the provided rentID and customer.");
}
$query->close();

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payType = $_POST['payType'] ?? '';
    $payMethod = $_POST['payMethod'] ?? '';

    if ($payType && $payMethod) {
        $payTotal = ($payType === 'deposit') ? $rentDepo : $rentTotal;
        $payBalance = $rentTotal - $payTotal;
        $payDate = date('Y-m-d');
        $payTime = date('H:i:s');
        $payStatus = "Pending";

        // Insert payment record
        $insertQuery = $connect->prepare(
            "INSERT INTO payment (custID, rentID, payDate, payTime, payType, payMethod, payTotal, payBalance, payStatus) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insertQuery->bind_param("sissssdds", $custID, $rentID, $payDate, $payTime, $payType, $payMethod, $payTotal, $payBalance, $payStatus);
        if ($insertQuery->execute()) {
            // Get the last inserted payID
            $payID = $insertQuery->insert_id;

            // Debugging log (optional)
            error_log("Payment recorded: rentID=$rentID, payID=$payID");

            // Construct the redirect URL with both rentID and payID
            $redirectUrl = "orderConfirm.php?rentID=" . urlencode($rentID) . "&payID=" . urlencode($payID);

            // Redirect to the constructed URL
            header("Location: " . $redirectUrl);
            exit();
        } else {
            die("Error: Could not record payment. " . $insertQuery->error);
        }
    } else {
        echo "Error: Payment type or method not provided.";
    }
} else {
    // Optional debugging log
    error_log("Payment form was not submitted properly.");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-7">
                <h4>Rental Id: #<?php echo htmlspecialchars($_SESSION['rentID']); ?> </h4>
                <form>
                    <div class="row mb-3">
                        <label for="custName" class="col-md-3 col-form-label">Customer Name</label>
                        <div class="col-md-9">
                            <input type="text" id="custName" class="form-control"
                                value="<?php echo htmlspecialchars($_SESSION['custName']); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="custPhone" class="col-md-3 col-form-label">Contact Number</label>
                        <div class="col-md-9">
                            <input type="text" id="custPhone" class="form-control"
                                value="<?php echo htmlspecialchars($_SESSION['custPhone'] ?? ''); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="custEmail" class="col-md-3 col-form-label">Email</label>
                        <div class="col-md-9">
                            <input type="email" id="custEmail" class="form-control"
                                value="<?php echo htmlspecialchars($_SESSION['custEmail'] ?? ''); ?>" readonly>

                        </div>
                    </div>
                </form>
                <hr>
                <h4>Payment Form</h4>
                <form action="custPay.php" method="POST" onsubmit="confirmPayment(event)">
                    <div class="payment-section mb-4">
                        <h1>Payment Type:</h1>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check border p-3 rounded">
                                    <input class="form-check-input" type="radio" name="payType" value="deposit"
                                        id="deposit" onclick="toggleFields()" checked
                                        style="width: 20px; height: 20px; cursor: pointer">
                                    <label class="form-check-label" for="deposit"
                                        style="font-size: 1.0rem; cursor: pointer;">Deposit</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check border p-3 rounded">
                                    <input class="form-check-input" type="radio" name="payType" value="Full Payment"
                                        id="fullPayment" onclick="toggleFields()"
                                        style="width: 20px; height: 20px; cursor: pointer">
                                    <label class="form-check-label" for="fullPayment"
                                        style="font-size: 1.0rem; cursor: pointer;">Full Payment</label>
                                </div>
                            </div>
                            <div id="remainingBalance" class="mt-3">
                                <h5 style="font-size: 1rem; font-weight: 600; color: #d9534f;">
                                    Balance: RM <?php echo number_format($rentTotal - $rentDepo, 2); ?>
                                </h5>
                            </div>

                        </div>
                    </div>
                    <div class="payment-section mb-4">
                        <h1>Payment Method:</h1>
                        <div class="row">
                            <!-- Column 1: Card -->
                            <div class="col-6">
                                <div class="form-check border p-3 rounded">
                                    <input class="form-check-input" type="radio" name="payMethod" value="card" id="card"
                                        onclick="toggleFields()" checked
                                        style="width: 20px; height: 20px; cursor: pointer;">
                                    <label class="form-check-label ms-2" for="card"
                                        style="font-size: 1.0rem; cursor: pointer;">
                                        Card
                                    </label>
                                </div>
                            </div>
                            <!-- Column 2: Cash -->
                            <div class="col-6">
                                <div class="form-check border p-3 rounded">
                                    <input class="form-check-input" type="radio" name="payMethod" value="cash" id="cash"
                                        onclick="toggleFields()" style="width: 20px; height: 20px; cursor: pointer">
                                    <label class="form-check-label ms-2" for="cash"
                                        style="font-size: 1.0rem; cursor: pointer;">Cash</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="cardFields" style="display: block;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cardNumber" class="form-label">Credit Card Number</label>
                                <input type="text" id="cardNumber" name="cardNumber" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expiration" class="form-label">Expiration</label>
                                <input type="text" id="expiration" name="expiration" class="form-control"
                                    placeholder="MM/YY">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cardName" class="form-label">Name on Card</label>
                                <input type="text" id="cardName" name="cardName" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="***">
                            </div>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-warning btn-lg w-100">Pay Now</button>
                </form>
            </div>

            <div class="col-md-5 py-5">
                <div class="summary p-4 shadow rounded" style="background-color: #fdfdfd; border: 2px solid #eaeaea;">
                    <h5 class="text-center mb-4" style="font-size: 1.5rem; font-weight: 700; color: #333;">SUMMARY</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size: 1rem; font-weight: 500; color: #555;">Subtotal:</span>
                        <span style="font-size: 1.1rem; font-weight: bold; color: #333;">RM
                            <?php echo number_format($rentTotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size: 1rem; font-weight: 500; color: #555;">Deposit (50%):</span>
                        <span style="font-size: 1.1rem; font-weight: bold; color: #333;">RM
                            <?php echo number_format($rentDepo, 2); ?></span>
                    </div>
                    <hr style="border-top: 2px solid #e0e0e0; margin: 20px 0;">
                    <div class="d-flex justify-content-between mt-3">
                        <h5 style="font-size: 1.25rem; font-weight: 700; color: #444;">Balance:</h5>
                        <h5 style="font-size: 1.25rem; font-weight: 700; color: #d9534f;">
                            RM <?php echo number_format($rentDepo, 2); ?></h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.all.min.js"></script>
    <script>
        function toggleFields() {
            const payType = document.querySelector('input[name="payType"]:checked').value;
            const remainingBalance = document.getElementById('remainingBalance');
            remainingBalance.style.display = (payType === 'deposit') ? 'block' : 'none';
            const payMethod = document.querySelector('input[name="payMethod"]:checked').value;
            const cardFields = document.getElementById('cardFields');
            const cardNumberField = document.getElementById('cardNumber');
            const expirationField = document.getElementById('expiration');
            const cardNameField = document.getElementById('cardName');
            const cvvField = document.getElementById('cvv');

            if (payMethod === 'card') {
                cardFields.style.display = 'block';
                cardNumberField.required = true;
                expirationField.required = true;
                cardNameField.required = true;
                cvvField.required = true;
            } else {
                cardFields.style.display = 'none';
                cardNumberField.required = false;
                expirationField.required = false;
                cardNameField.required = false;
                cvvField.required = false;
            }
        }
    </script>
    <script>
        function validatePaymentForm() {
            const payMethod = document.querySelector('input[name="payMethod"]:checked').value;

            if (payMethod === 'card') {
                const cardNumber = document.getElementById('cardNumber').value;
                const expiration = document.getElementById('expiration').value;
                const cardName = document.getElementById('cardName').value;
                const cvv = document.getElementById('cvv').value;

                if (!cardNumber || !expiration || !cardName || !cvv) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Input',
                        text: 'Please fill out all required card fields before submitting.'
                    });
                    return false;  // Prevent form submission
                }
            }
            return true;  // Allow form submission
        }
    </script>
    <script>
        function confirmPayment(event) {
            // Prevent form submission initially
            event.preventDefault();

            // First confirmation dialog
            Swal.fire({
                title: 'Confirm Payment',
                text: 'Do you want to proceed with the payment?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Second SweetAlert after confirmation
                    Swal.fire({
                        title: 'Payment Successful!',
                        text: 'Your payment has been completed. You will now be redirected.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000, // Display for 3 seconds
                        timerProgressBar: true,
                    }).then(() => {
                        // Submit the form programmatically after the confirmation
                        event.target.submit();
                    });
                }
            });
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #333;
        }

        .container {
            margin-top: 100px;
        }

        h1 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #eee;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
        }

        input.form-control {
            border-radius: 20px;
            padding: 10px;

        }

        input.form-control:focus {
            border-color: #333;
            box-shadow: 0px 0px 4px rgba(51, 51, 51, 0.4);
        }

        hr {
            border: 1px solid #aaa;
            margin: 40px 0;
        }

        .payment-section label {
            font-weight: 600;
            color: #333;
        }

        .radio-option {
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 13px 20px;
            margin: 10px 10px;
            cursor: pointer;
            width: 300px;
            height: 50px;
        }

        .radio-option input[type="radio"] {
            display: inline-block;
            margin-right: 10px;
        }

        .radio-option input[type="radio"]:checked+span {
            border-color: #000;
            font-weight: bold;
        }

        .radio-option span {
            display: inline-block;
            vertical-align: middle;
        }

        button.btn-primary {
            background-color: #333;
            border-color: #333;
            color: #fff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        button.btn-primary:hover {
            background-color: #555;
            border-color: #555;
        }

        .summary {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .summary h5 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .summary p {
            font-size: 1rem;
            color: #555;
        }

        .summary hr {
            border: 1px solid #aaa;
            margin: 40px 0;
        }

        #cardFields input {
            background-color: #f9f9f9;
        }

        #remainingBalance {
            color: #d9534f;
            margin: 0 5px;
            font-size: 1rem;
            font-weight: 600;
        }
    </style>
</body>

</html>