<?php
include "../config/db.php";
include "../../auth.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Tambah departemen
    if ($action === 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $query = "INSERT INTO department (name, is_active) VALUES (?, 1)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $name);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Department added successfully!');window.location.href = '../update-departement-name.php';</script>";
        } else {
            echo "<script>alert('Error adding department!');window.location.href = '../update-departement-name.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
    // Hapus departemen (soft delete)
    else if ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "UPDATE department SET is_active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Department deleted successfully!');window.location.href = '../update-departement-name.php';</script>";
        } else {
            echo "<script>alert('Error deleting department!');window.location.href = '../update-departement-name.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>