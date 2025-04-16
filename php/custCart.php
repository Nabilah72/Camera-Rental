<?php
include('connect.php');
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if user is logged in
if (!isset($_SESSION['custName'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user profile image
$stmt = $connect->prepare("SELECT custImage FROM customer WHERE custID = ?");
$stmt->bind_param("s", $_SESSION['custID']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$custImage = !empty($user['custImage']) ? $user['custImage'] : '../images/default-avatar.jpg';

// Add item to cart
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['prodID'])) {
    $days = isset($_GET['days']) ? (int) $_GET['days'] : 0;

    $_SESSION['cart'][] = [
        'prodID' => $_GET['prodID'],
        'prodBrand' => $_GET['prodBrand'],
        'prodModel' => $_GET['prodModel'],
        'prodImage' => $_GET['prodImage'],
        'prodPrice' => (float) $_GET['prodPrice'],
        'quantity' => (int) $_GET['quantity'],
        'days' => $days,
        'dateIn' => $_GET['dateIn'],
        'dateOut' => $_GET['dateOut'],
    ];
    header("Location: custCart.php");
    exit();
}

// Remove item(s) from cart
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'remove') {
    if (!empty($_GET['items'])) {
        // Decode the items string into an array
        $itemsToRemove = explode(',', $_GET['items']);

        // Loop through each item and remove it based on prodID, dateIn, and dateOut
        foreach ($itemsToRemove as $item) {
            list($prodID, $prodDateIn, $prodDateOut) = explode('|', $item);

            // Filter the cart and remove items matching prodID, dateIn, and dateOut
            $_SESSION['cart'] = array_values(array_filter(
                $_SESSION['cart'],
                fn($cartItem) => !(
                    $cartItem['prodID'] === $prodID &&
                    $cartItem['dateIn'] === $prodDateIn &&
                    $cartItem['dateOut'] === $prodDateOut
                )
            ));
        }
    }

    // Redirect to the cart page after removal
    header("Location: custCart.php");
    exit();
}
$rentDepo = 0;
$rentTotal = 0;
$subtotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $dateIn = DateTime::createFromFormat('Y-m-d', $item['dateIn']);
    $dateOut = DateTime::createFromFormat('Y-m-d', $item['dateOut']);

    if ($dateIn && $dateOut) {
        $interval = $dateIn->diff($dateOut);
        $days = max($interval->days, 1);
    } else {
        $days = 1;
    }

    $dailyRate = $item['prodPrice'];
    $quantity = $item['quantity'];

    $subtotal += $dailyRate * $days * $quantity;
    $rentDepo = $subtotal * 0.5;
    $rentTotal = $subtotal;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {

    if (empty($_SESSION['cart'])) {
        die("Error: Your cart is empty. Please add items before checkout.");
    }

    $rentStatus = "Not Returned";
    $rentTotal = $subtotal;

    if ($rentTotal <= 0 || $rentDepo <= 0) {
        die("Error: Total or deposit amounts are missing or invalid.");
    }

    $rentDetails = json_encode($_SESSION['cart'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Set a dummy admin ID if needed (replace $adminID with real admin ID logic)
    $adminID = null;

    // Insert rental into the database
    $stmt = $connect->prepare("INSERT INTO rental (custID, adminID, rentStatus, rentDepo, rentTotal, rentDetails) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdds", $_SESSION['custID'], $adminID, $rentStatus, $rentDepo, $rentTotal, $rentDetails);

    if ($stmt->execute()) {
        // Retrieve the last inserted rentID
        $rentID = $connect->insert_id;

        // Ensure rentID is fetched successfully
        if ($rentID) {
            // Update product quantities for each item in the cart
            foreach ($_SESSION['cart'] as $item) {
                $stmt_update = $connect->prepare("UPDATE product SET currentQty = currentQty - ? WHERE prodID = ?");
                $stmt_update->bind_param("is", $item['quantity'], $item['prodID']);
                $stmt_update->execute();
                $stmt_update->close();
            }

            // Clear the cart after successful checkout
            unset($_SESSION['cart']);

            // Redirect to the payment page with rentID
            header(header: "Location: custPay.php?rentID=" . urlencode($rentID));
            exit();
        } else {
            die("Error: Unable to fetch rentID after rental insertion.");
        }
    } else {
        echo 'Error inserting data: ' . $stmt->error;
    }
    $stmt->close();
}

$groupedCart = [];
foreach ($_SESSION['cart'] as $item) {
    $key = $item['prodID'] . '|' . $item['dateIn'] . '|' . $item['dateOut'];
    if (isset($groupedCart[$key])) {
        $groupedCart[$key]['quantity'] += $item['quantity'];
    } else {
        $groupedCart[$key] = $item;
    }
}
$_SESSION['cart'] = array_values($groupedCart);

// Replace the cart session with the grouped cart
$_SESSION['cart'] = array_values($groupedCart);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <img width="70" height="70" src="../images/logo.png" alt="Company Logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
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
                <a href="custProfile.php"><img style="cursor: pointer" src="<?php echo htmlspecialchars($custImage); ?>"
                        alt="Profile Picture" class="rounded-circle ms-2" width="40" height="40"></a>
            </span>
        </div>
    </nav>

    <div class="container cart-container py-5">
        <div class="row">
            <div class="col-lg-7">
                <h3>Your Cart (<?php echo count($_SESSION['cart']); ?>)</h3>
                <div class="d-flex text-align center justify-content-between selector-container">
                    <div>
                        <input type="checkbox" id="main-selector" onchange="toggleRemoveButtonForAll()">
                        <label for="main-selector">Select All</label>
                    </div>
                    <button style="margin: 10px;" id="remove-btn" class="btn btn-md btn-danger" disabled
                        onclick="removeSelectedItems()">Remove</button>
                </div>

                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="item-selector" onchange="toggleRemoveButton()"
                            value="<?php echo htmlspecialchars(json_encode(['prodID' => $item['prodID'], 'prodDateIn' => $item['dateIn'], 'prodDateOut' => $item['dateOut']])); ?>">
                        <div class="d-flex align-items-center cart-item">
                            <img src="<?php echo htmlspecialchars($item['prodImage']); ?>" alt="Product Image">
                            <div class="cart-info">
                                <div class="d-flex justify-content-between">
                                    <p><strong>Pickup Date:</strong>
                                        <?php
                                        $dateIn = DateTime::createFromFormat('Y-m-d', $item['dateIn']);
                                        $formattedDateIn = $dateIn ? $dateIn->format('d/m/Y') : 'Invalid date';
                                        echo $formattedDateIn;
                                        ?>
                                    </p>
                                    <p><strong>Return Date:</strong>
                                        <?php
                                        $dateOut = DateTime::createFromFormat('Y-m-d', $item['dateOut']);
                                        $formattedDateOut = $dateOut ? $dateOut->format('d/m/Y') : 'Invalid date';
                                        echo $formattedDateOut;
                                        ?>
                                    </p>
                                </div>
                                <hr style="margin:10px;">
                                <div class="cart-details text-center">
                                    <table class="cart-table">
                                        <thead>
                                            <tr>
                                                <th><?php echo htmlspecialchars($item['prodID']); ?></th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['prodBrand'] . ' ' . $item['prodModel']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                <td>RM<?php echo number_format($item['prodPrice'], 2); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-5">
                <div class="summary">
                    <h4>Summary</h4>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Subtotal (<?php echo count($_SESSION['cart']); ?> items)</td>
                                <td class="text-end">RM <?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Deposit (50%)</td>
                                <td class="text-end">RM <?php echo number_format($rentDepo, 2); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Remaining Balance</th>
                                <th class="text-end">RM <?php echo number_format($rentTotal - $rentDepo, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <form method="POST" action="custCart.php">
                        <button type="submit" name="checkout" class="btn btn-warning btn-lg w-100">CHECKOUT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleRemoveButtonForAll() {
            const isChecked = document.getElementById('main-selector').checked;
            const itemSelectors = document.querySelectorAll('.item-selector');

            itemSelectors.forEach(selector => {
                selector.checked = isChecked;
            });

            toggleRemoveButton();
        }

        function toggleRemoveButton() {
            const selectors = document.querySelectorAll('.item-selector');
            const removeBtn = document.getElementById('remove-btn');
            const isAnySelected = Array.from(selectors).some(selector => selector.checked);
            removeBtn.disabled = !isAnySelected;
        }

        function removeSelectedItems() {
            const selectedItems = Array.from(document.querySelectorAll('.item-selector:checked'))
                .map(selector => JSON.parse(selector.value));  // Decode the JSON value

            if (selectedItems.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to remove selected items from your cart!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, keep them',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Convert selectedItems to a format suitable for URL (including prodID, prodDateIn, and prodDateOut)
                        const itemsToRemove = selectedItems.map(item => {
                            return `${encodeURIComponent(item.prodID)}|${encodeURIComponent(item.prodDateIn)}|${encodeURIComponent(item.prodDateOut)}`;
                        }).join(',');

                        // Use the correct URL format
                        const url = `custCart.php?action=remove&items=${encodeURIComponent(itemsToRemove)}`;
                        window.location.href = url;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No items selected for removal!',
                });
            }
        }
    </script>

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
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 2rem;
            animation: fade-in 1s ease-out;
            text-transform: uppercase;
        }

        h5 {
            font-size: 1.3rem;
            font-weight: 600;
            animation: fade-in 1s ease-out;
            text-transform: uppercase;
        }

        p {
            font-size: 1.2rem;
            line-height: 2rem;
            animation: fade-in 1.5s ease-out;
        }

        .cart-container {
            margin-top: 80px;
        }

        .cart-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            background-color: #fff;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .cart-info {
            flex-grow: 1;
            padding: 0 15px;
        }

        .cart-info h6,
        .cart-info p {
            margin: 0;
            font-size: 14px;
        }

        .cart-info h6 {
            font-size: 16px;
            font-weight: bold;
        }

        .cart-details {
            text-align: center;
            font-size: 14px;
        }

        .cart-table {
            width: 100%;
            text-align: center;
            table-layout: fixed;
        }

        .cart-table th {
            font-weight: bold;
        }

        .summary {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #fff;
        }

        .summary h4 {
            text-align: center;
            margin-bottom: 20px;
        }

        .summary table {
            width: 100%;
        }

        .summary .btn {
            font-size: 16px;
            font-weight: bold;
        }

        .item-selector {
            margin-right: 15px;
        }

        .selector-container {
            cursor: pointer;
            align-items: center;
        }
    </style>
</body>

</html>