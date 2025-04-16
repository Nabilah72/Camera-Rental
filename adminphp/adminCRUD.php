<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addAdmin'])) {
        // Add New Admin
        $adminID = $_POST['adminID'];
        $adminName = $_POST['adminName'];
        $adminPhone = $_POST['adminPhone'];
        $adminEmail = $_POST['adminEmail'];
        $adminPassword = password_hash($_POST['adminPassword'], PASSWORD_BCRYPT);
        $adminStatus = $_POST['adminStatus'];

        $sql = "INSERT INTO admin (adminID, adminName, adminPhone, adminEmail, adminPassword, adminStatus) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssssss", $adminID, $adminName, $adminPhone, $adminEmail, $adminPassword, $adminStatus);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Admin added successfully!";
        } else {
            $_SESSION['error'] = "Error adding admin: " . $connect->error;
        }
        $stmt->close();
        header("Location: admin.php");
        exit();
    }

    if (isset($_POST['editAdmin'])) {
        // Edit Admin Info
        $adminID = $_POST['adminID'];
        $adminPhone = $_POST['adminPhone'];
        $adminEmail = $_POST['adminEmail'];
        $adminPassword = $_POST['adminPassword'];
        $adminStatus = $_POST['adminStatus'];
        $adminPic = null;

        // Handle adminPic upload
        if (isset($_FILES['adminPic']) && $_FILES['adminPic']['error'] == 0) {
            $targetDir = "C:/xampp/htdocs/CAMERA RENTAL/Images/";
            $targetFile = $targetDir . basename($_FILES['adminPic']['name']);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedTypes)) {
                $_SESSION['error'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                header("Location: admin.php");
                exit();
            }

            // Move uploaded file
            if (!move_uploaded_file($_FILES['adminPic']['tmp_name'], $targetFile)) {
                $_SESSION['error'] = "Error uploading file.";
                header("Location: admin.php");
                exit();
            }
            $adminPic = $targetFile;
        }

        // Update query
        if ($adminPic) {
            $sql = "UPDATE admin SET adminPhone = ?, adminEmail = ?, adminPassword = ?, adminStatus = ?, adminPic = ? WHERE adminID = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("ssssss", $adminPhone, $adminEmail, $adminPassword, $adminStatus, $adminPic, $adminID);
        } else {
            $sql = "UPDATE admin SET adminPhone = ?, adminEmail = ?, adminPassword = ?, adminStatus = ? WHERE adminID = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("sssss", $adminPhone, $adminEmail, $adminPassword, $adminStatus, $adminID);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Admin updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating admin: " . $connect->error;
        }
        $stmt->close();
        header("Location: admin.php");
        exit();
    }
}
?>