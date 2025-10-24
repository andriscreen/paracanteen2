<?php
include "../config/db.php";
include "../../auth.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Tambah plant
    if ($action === 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $query = "INSERT INTO plant (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $name);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Plant added successfully!');window.location.href = '../update-plant-name.php';</script>";
        } else {
            echo "<script>alert('Error adding plant!');window.location.href = '../update-plant-name.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
    // Hapus plant
    else if ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "DELETE FROM plant WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Plant deleted successfully!');window.location.href = '../update-plant-name.php';</script>";
        } else {
            echo "<script>alert('Error deleting plant!');window.location.href = '../update-plant-name.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>