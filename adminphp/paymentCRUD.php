<?php
session_start();
include "connection.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editPayment'])) {
    $payID = $_POST['payID'];
    $payStatus = $_POST['payStatus'];

    $query = "SELECT payTotal, payBalance, payStatus FROM payment WHERE payID = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $payID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
        $currentPayStatus = $payment['payStatus'];
        $payTotal = $payment['payTotal'];
        $payBalance = $payment['payBalance'];

        if ($currentPayStatus === "Completed") {
            $_SESSION['error_message'] = "Cannot edit status. The payment is already completed.";
            header("Location: payment.php");
            exit();
        }

        if ($payStatus === "Completed") {
            $payTotal += $payBalance; // Add remainBal to payTotal
            $payBalance = 0;         // Set remainBal to 0
        }

        $updateQuery = "UPDATE payment SET payStatus = ?, payTotal = ?, payBalance = ? WHERE payID = ?";
        $updateStmt = $connect->prepare($updateQuery);
        $updateStmt->bind_param("sdii", $payStatus, $payTotal, $payBalance, $payID);

        if ($updateStmt->execute()) {
            $_SESSION['success_message'] = "Payment status updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating payment status: " . $updateStmt->error;
        }

        $updateStmt->close();
    } else {
        $_SESSION['error_message'] = "Payment record not found.";
    }

    $stmt->close();
    $connect->close();

    header("Location: payment.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: payment.php");
    exit();
}
