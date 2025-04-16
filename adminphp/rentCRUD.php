<?php
session_start();
include "connection.php";

if (isset($_POST['editRental'])) {
    // Get the adminID, rentID, and rentStatus
    $adminID = $_POST['adminID'];
    $rentID = $_POST['rentID'];
    $rentStatus = $_POST['rentStatus'];

    // Validate inputs
    if ($adminID && $rentID && $rentStatus) {
        // Update rental status
        $updateQuery = "UPDATE rental SET rentStatus = ?, adminID = ? WHERE rentID = ?";
        $stmt = $connect->prepare($updateQuery);
        $stmt->bind_param("ssi", $rentStatus, $adminID, $rentID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Rental status updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update rental status.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid data provided.";
    }
}

header("Location: rental.php"); // Redirect back to the rental page
exit();
