<?php
session_start();
include "connection.php";

// Process form when 'editAdmin' is submitted
if (isset($_POST['editAdmin'])) {
    $adminID = $_POST['adminID'];
    $adminPhone = $_POST['adminPhone'];
    $adminEmail = $_POST['adminEmail'];
    $adminStatus = $_POST['adminStatus'];

    // Update the admin info in the database
    $sql = "UPDATE admin SET adminPhone = ?, adminEmail = ?, adminStatus = ? WHERE adminID = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssss", $adminPhone, $adminEmail, $adminStatus, $adminID);
    if ($stmt->execute()) {
        // Update session variables with new data
        $_SESSION['adminPhone'] = $adminPhone;
        $_SESSION['adminEmail'] = $adminEmail;
        $_SESSION['adminStatus'] = $adminStatus;

        // Set success message
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update profile.";
    }

    // Redirect back to the profile page
    header("Location: profile.php");
    exit();
}
?>
