<?php
include "../config/db.php";
include "../../auth.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Tambah place
    if ($action === 'add') {
        $plant_id = mysqli_real_escape_string($conn, $_POST['plant_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $query = "INSERT INTO place (plant_id, name) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $plant_id, $name);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Place added successfully!');window.location.href = '../update-place-name.php';</script>";
        } else {
            echo "<script>alert('Error adding place!');window.location.href = '../update-place-name.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
    // Hapus place
    else if ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "DELETE FROM place WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Place deleted successfully!');window.location.href = '../update-place-name.php';</script>";
        } else {
            echo "<script>alert('Error deleting place!');window.location.href = '../update-place-name.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>