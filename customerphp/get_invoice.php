<?php
session_start();
include('connect.php');
require_once('../tcpdf.php');

// Ensure user is logged in
if (!isset($_SESSION['custID'])) {
    header('Location: login.php');
    exit;
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

$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$sortDirection = $sortOrder === 'oldest' ? 'ASC' : 'DESC';

$rentStatus = isset($_GET['rentStatus']) ? $_GET['rentStatus'] : '';
$statusCondition = !empty($rentStatus) ? " AND rental.rentStatus = ?" : '';

// Fetch rental and payment details with status filter
$stmt = $connect->prepare("
    SELECT rental.rentID, rental.rentDepo, rental.rentTotal, rental.rentDetails, 
           rental.rentStatus, MAX(payment.payDate) AS payDate, payment.payStatus, payment.payMethod, payment.payType, payment.payTotal, payment.payBalance
    FROM rental
    LEFT JOIN payment ON rental.custID = payment.custID
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
    header('Location: custViewHistory.php');
    exit;
}

// Initialize rental for PDF generation
$rental = $rentals[0]; // Use the first rental for example

// Fetch rental details
$orderDetailsStmt = $connect->prepare("SELECT * FROM rental WHERE rentID = ?");
$orderDetailsStmt->bind_param("s", $rental['rentID']);
$orderDetailsStmt->execute();
$orderDetailsResult = $orderDetailsStmt->get_result();

// Fetch all the details, if available
$orderDetails = $orderDetailsResult->num_rows > 0 ? $orderDetailsResult->fetch_all(MYSQLI_ASSOC) : [];

// Decode the rentDetails (assuming it's stored in JSON format in the database)
foreach ($orderDetails as &$item) {
    if (!empty($item['rentDetails'])) {
        $decodedDetails = json_decode($item['rentDetails'], true);
        $item['rentDetails'] = is_array($decodedDetails) ? $decodedDetails : [];
    } else {
        $item['rentDetails'] = []; // Assign empty array for missing details
    }
}


// TCPDF setup
$pdf = new \TCPDF();
$pdf->SetCreator('TCPDF');
$pdf->SetTitle('Rental Receipt');
$pdf->SetSubject('Rental Receipt for Rental ID ' . $rental['rentID']);
$pdf->SetAuthor('Your Company Name');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Title and Rental Info
// Title and Rental Info
$pdf->Cell(0, 10, 'Rental Receipt', 0, 1, 'C');
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Rental ID: ' . $rental['rentID'], 0, 1);
$pdf->Cell(0, 10, 'Rental Status: ' . (isset($rental['rentStatus']) ? $rental['rentStatus'] : 'N/A'), 0, 1); // Added check for rentStatus

// Rental details table
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Rental Details:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 10, 'Product', 1);
$pdf->Cell(40, 10, 'Brand & Model', 1);
$pdf->Cell(20, 10, 'Quantity', 1);
$pdf->Cell(30, 10, 'Price/Day (RM)', 1);
$pdf->Cell(20, 10, 'Days', 1);
$pdf->Cell(30, 10, 'Subtotal (RM)', 1);
$pdf->Ln();

// Loop through the decoded rentDetails and populate the table
foreach ($orderDetails as $item) {
    if (!empty($item['rentDetails'])) {
        // Loop through each product in the rentDetails array
        foreach ($item['rentDetails'] as $details) {
            $prodPrice = $details['prodPrice'] ?? 0;
            $days = $details['days'] ?? 0;
            $quantity = $details['quantity'] ?? 0;
            $prodBrand = $details['prodBrand'] ?? 'N/A';
            $prodModel = $details['prodModel'] ?? 'N/A';

            $subtotal = $prodPrice * $days * $quantity;

            $pdf->Cell(30, 10, $prodBrand, 1);
            $pdf->Cell(40, 10, $prodModel, 1);
            $pdf->Cell(20, 10, $quantity, 1);
            $pdf->Cell(30, 10, number_format($prodPrice, 2), 1);
            $pdf->Cell(20, 10, $days, 1);
            $pdf->Cell(30, 10, number_format($subtotal, 2), 1);
            $pdf->Ln();
        }
    }
}

// Total amounts
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Total Rent: RM ' . number_format($rental['rentTotal'] ?? 0, 2), 0, 1);
$pdf->Cell(0, 10, 'Total Paid: RM ' . number_format($rental['payTotal'] ?? 0, 2), 0, 1); // Check for payTotal
$pdf->Cell(0, 10, 'Remaining Balance: RM ' . number_format($rental['payBalance'] ?? 0, 2), 0, 1); // Check for payBalance

// Output PDF
$pdf->Output('receipt_' . $rental['rentID'] . '.pdf', 'I');
?>