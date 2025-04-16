<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addShelf'])) {
        // Add New Shelf
        $shelfID = $_POST['shelfID'];
        $shelfName = $_POST['shelfName'];

        $sql = "INSERT INTO shelf (shelfID, shelfName) VALUES (?, ?)";
        $stmt = $connect->prepare($sql);

        $stmt->bind_param("ss", $shelfID, $shelfName);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Shelf added successfully!";
        } else {
            $_SESSION['error'] = "Error adding shelf: " . $connect->error;
        }
        $stmt->close();
        header("Location: shelff.php");
        exit();
    }

    if (isset($_POST['editShelf'])) {
        // Edit Shelf Info
        $shelfID = $_POST['shelfID'];
        $shelfName = $_POST['shelfName'];

        $sql = "UPDATE shelf SET shelfName = ? WHERE shelfID = ?";
        $stmt = $connect->prepare($sql);

        $stmt->bind_param("ss", $shelfName, $shelfID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Shelf updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating shelf: " . $connect->error;
        }
        $stmt->close();
        header("Location: shelff.php");
        exit();
    }

    if (isset($_POST['deleteShelf'])) {
        // Delete Shelf
        $shelfID = $_POST['shelfID'];

        $sql = "DELETE FROM shelf WHERE shelfID = ?";
        $stmt = $connect->prepare($sql);

        $stmt->bind_param("s", $shelfID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Shelf deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting shelf: " . $connect->error;
        }
        $stmt->close();
        header("Location: shelff.php");
        exit();
    }
}
?>
