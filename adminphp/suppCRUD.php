<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addSupp'])) {
        // Add new supplier
        $suppID = $_POST['suppID'];
        $suppName = $_POST['suppName'];
        $suppPhone = $_POST['suppPhone'];
        $suppEmail = $_POST['suppEmail'];
        $suppStatus = $_POST['suppStatus'];

        // Check for duplicate supplier ID
        $sqlCheck = "SELECT COUNT(*) FROM supplier WHERE suppID = ?";
        $stmtCheck = $connect->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $suppID);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($count > 0) {
            // If duplicate, redirect with error message
            header("Location: supplier.php?error=Duplicate+Supplier+ID");
            exit();
        }

        $sql = "INSERT INTO supplier (suppID, suppName, suppPhone, suppEmail, suppStatus) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssss", $suppID, $suppName, $suppPhone, $suppEmail, $suppStatus);

        if ($stmt->execute()) {
            header("Location: supplier.php?message=Supplier+added+successfully");
            exit();
        } else {
            header("Location: supplier.php?error=Error+adding+supplier");
            exit();
        }
    }

    if (isset($_POST['editSupp'])) {
        // Edit supplier information
        $suppID = $_POST['suppID'];
        $suppName = $_POST['suppName'];
        $suppPhone = $_POST['suppPhone'];
        $suppEmail = $_POST['suppEmail'];
        $suppStatus = $_POST['suppStatus'];

        $sql = "UPDATE supplier SET suppName = ?, suppPhone = ?, suppEmail = ?, suppStatus = ? WHERE suppID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssss", $suppName, $suppPhone, $suppEmail, $suppStatus, $suppID);

        if ($stmt->execute()) {
            header("Location: supplier.php?message=Supplier+updated+successfully");
            exit();
        } else {
            header("Location: supplier.php?error=Error+updating+supplier");
            exit();
        }
    }

    if (isset($_POST['deleteSupp'])) {
        // Delete supplier
        $suppID = $_POST['suppID'];

        $sql = "DELETE FROM supplier WHERE suppID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $suppID);

        if ($stmt->execute()) {
            header("Location: supplier.php?message=Supplier+deleted+successfully");
            exit();
        } else {
            header("Location: supplier.php?error=Error+deleting+supplier");
            exit();
        }
    }
}

$connect->close();

?>
