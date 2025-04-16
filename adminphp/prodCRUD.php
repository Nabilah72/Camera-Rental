<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addProduct'])) {
        // Add New Product
        $prodID = $_POST['prodID'];
        $suppID = $_POST['suppID'];
        $shelfID = $_POST['shelfID'];
        $prodType = $_POST['prodType'];
        $prodBrand = $_POST['prodBrand'];
        $prodModel = $_POST['prodModel'];
        $prodPrice = $_POST['prodPrice'];
        $prodQty = $_POST['prodQty'];
        $prodStatus = $_POST['prodStatus'];

        // Check for duplicate prodID
        $sqlCheckProd = "SELECT COUNT(*) FROM product WHERE prodID = ?";
        $stmtCheckProd = $connect->prepare($sqlCheckProd);
        $stmtCheckProd->bind_param("s", $prodID);
        $stmtCheckProd->execute();
        $stmtCheckProd->bind_result($countProd);
        $stmtCheckProd->fetch();
        $stmtCheckProd->close();

        if ($countProd > 0) {
            $_SESSION['error'] = "Duplicate Product ID.";
            header("Location: product.php");
            exit();
        }

        // Check if suppID exists
        $sqlCheckSupp = "SELECT COUNT(*) FROM supplier WHERE suppID = ?";
        $stmtCheckSupp = $connect->prepare($sqlCheckSupp);
        $stmtCheckSupp->bind_param("s", $suppID);
        $stmtCheckSupp->execute();
        $stmtCheckSupp->bind_result($countSupp);
        $stmtCheckSupp->fetch();
        $stmtCheckSupp->close();

        if ($countSupp == 0) {
            $_SESSION['error'] = "Supplier ID does not exist.";
            header("Location: product.php");
            exit();
        }

        // Check if shelfID exists
        $sqlCheckShelf = "SELECT COUNT(*) FROM shelf WHERE shelfID = ?";
        $stmtCheckShelf = $connect->prepare($sqlCheckShelf);
        $stmtCheckShelf->bind_param("s", $shelfID);
        $stmtCheckShelf->execute();
        $stmtCheckShelf->bind_result($countShelf);
        $stmtCheckShelf->fetch();
        $stmtCheckShelf->close();

        if ($countShelf == 0) {
            $_SESSION['error'] = "Shelf ID does not exist.";
            header("Location: product.php");
            exit();
        }

        $sql = "INSERT INTO product (prodID, suppID, shelfID, prodType, prodBrand, prodModel, prodPrice, prodQty, prodStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssssssss", $prodID, $suppID, $shelfID, $prodType, $prodBrand, $prodModel, $prodPrice, $prodQty, $prodStatus);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product added successfully!";
        } else {
            $_SESSION['error'] = "Error adding product: " . $connect->error;
        }
        $stmt->close();
        header("Location: product.php");
        exit();
    }

    if (isset($_POST['editProduct'])) {
        // Edit Product Info
        $prodID = $_POST['prodID'];
        $prodType = $_POST['prodType'];
        $prodBrand = $_POST['prodBrand'];
        $prodModel = $_POST['prodModel'];
        $prodPrice = $_POST['prodPrice'];
        $prodQty = $_POST['prodQty'];
        $prodStatus = $_POST['prodStatus'];

        $sql = "UPDATE product SET prodType = ?, prodBrand = ?, prodModel = ?, prodPrice = ?, prodQty = ?, prodStatus = ? WHERE prodID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssssss", $prodType, $prodBrand, $prodModel, $prodPrice, $prodQty, $prodStatus, $prodID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating product: " . $connect->error;
        }
        $stmt->close();
        header("Location: product.php");
        exit();
    }

    if (isset($_POST['deleteProd'])) {
        // Delete Product
        $prodID = $_POST['prodID'];

        $sql = "DELETE FROM product WHERE prodID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $prodID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting product: " . $connect->error;
        }
        $stmt->close();
        header("Location: product.php");
        exit();
    }
}

$connect->close();
?>
