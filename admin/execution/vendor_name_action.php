<?php
include "../config/db.php";
include "../../auth.php";

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Handle Add Vendor
    if ($action === 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        // Insert data
        $query = "INSERT INTO nama_vendor (name, is_active) VALUES (?, 1)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $name);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Vendor name added successfully!');
                window.location.href = '../update-vendor-name.php';
            </script>";
        } else {
            echo "<script>
                alert('Error adding vendor name!');
                window.location.href = '../update-vendor-name.php';
            </script>";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Handle Delete Vendor
    else if ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        // Soft delete: set is_active = 0
        $query = "UPDATE nama_vendor SET is_active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Vendor name deleted successfully!');
                window.location.href = '../update-vendor-name.php';
            </script>";
        } else {
            echo "<script>
                alert('Error deleting vendor name!');
                window.location.href = '../update-vendor-name.php';
            </script>";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>