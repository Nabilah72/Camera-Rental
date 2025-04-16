<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addStaff'])) {
        // Add New Staff
        $staffID = $_POST['staffID'];
        $adminID = $_POST['adminID'];
        $staffName = $_POST['staffName'];
        $staffPhone = $_POST['staffPhone'];
        $staffEmail = $_POST['staffEmail'];
        $staffPassword = $_POST['staffPassword'];
        $staffStatus = $_POST['staffStatus'];

        // Check if the staffID already exists
        $sqlCheck = "SELECT COUNT(*) FROM staff WHERE staffID = ?";
        $stmtCheck = $connect->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $staffID);
        $stmtCheck->execute();
        $stmtCheck->bind_result($staffCount);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($staffCount > 0) {
            // If staffID already exists, show error message
            $_SESSION['error'] = "Staff ID already exists!";
            header("Location: staff.php");
            exit();
        }

        // Insert the new staff record if the staffID is unique
        $sql = "INSERT INTO staff (staffID, adminID, staffName, staffPhone, staffEmail, staffPassword, staffStatus) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssssss", $staffID, $adminID, $staffName, $staffPhone, $staffEmail, $staffPassword, $staffStatus);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Staff added successfully!";
        } else {
            $_SESSION['error'] = "Error adding staff: " . $connect->error;
        }
        $stmt->close();
        header("Location: staff.php");
        exit();
    }

    if (isset($_POST['editStaff'])) {
        // Edit Staff Info
        $staffID = $_POST['staffID'];
        $staffPhone = $_POST['staffPhone'];
        $staffEmail = $_POST['staffEmail'];
        $staffPassword = $_POST['staffPassword'];
        $staffStatus = $_POST['staffStatus'];
    
        $sql = "UPDATE staff SET staffPhone = ?, staffEmail = ?, staffPassword = ?, staffStatus = ? WHERE staffID = ?";
        $stmt = $connect->prepare($sql);
    
        // Correct bind_param to match the placeholders in the query
        $stmt->bind_param("sssss", $staffPhone, $staffEmail, $staffPassword, $staffStatus, $staffID);
    
        if ($stmt->execute()) {
            $_SESSION['success'] = "Staff updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating staff: " . $connect->error;
        }
        $stmt->close();
        header("Location: staff.php");
        exit();
    }

    if (isset($_POST['deleteStaff'])) {
        // Delete Staff
        $staffID = $_POST['staffID'];

        $sql = "DELETE FROM staff WHERE staffID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $staffID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Staff deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting staff: " . $connect->error;
        }
        $stmt->close();
        header("Location: staff.php");
        exit();
    }
}

// If the request method is not POST, redirect to the staff page
header("Location: staff.php");
exit();
?>
