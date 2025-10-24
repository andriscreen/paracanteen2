<?php
include "../config/db.php";
include "../../auth.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Tambah menu
    if ($action === 'add') {
        $week_id = mysqli_real_escape_string($conn, $_POST['week_id']);
        $day = mysqli_real_escape_string($conn, $_POST['day']);
        $menu_name = mysqli_real_escape_string($conn, $_POST['menu_name']);
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']); // Ambil keterangan dari input form

        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Delete existing menu and image for this week and day
            $delete_menu = "DELETE FROM menu WHERE week_id = ? AND day = ?";
            $stmt = mysqli_prepare($conn, $delete_menu);
            mysqli_stmt_bind_param($stmt, "is", $week_id, $day);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $delete_image = "DELETE FROM menu_images WHERE week_id = ? AND day = ?";
            $stmt = mysqli_prepare($conn, $delete_image);
            mysqli_stmt_bind_param($stmt, "is", $week_id, $day);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Insert new menu
            $query = "INSERT INTO menu (week_id, day, menu_name, keterangan) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "isss", $week_id, $day, $menu_name, $keterangan); // Bind keterangan
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Handle image upload
            if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] == 0) {
                $file = $_FILES['menu_image'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = "week{$week_id}_{$day}." . $ext;
                $target_path = "../../assets/img/menu/" . $filename;

                // Create directory if it doesn't exist
                if (!file_exists("../../assets/img/menu/")) {
                    mkdir("../../assets/img/menu/", 0777, true);
                }

                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $image_url = "assets/img/menu/" . $filename;
                    $query = "INSERT INTO menu_images (week_id, day, image_url) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "iss", $week_id, $day, $image_url);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            mysqli_commit($conn);
            echo "<script>alert('Menu added successfully!');window.location.href = '../update-menu.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Error adding menu!');window.location.href = '../update-menu.php';</script>";
        }
    }
    // Hapus menu
    else if ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        // Get menu details for image deletion
        $menu_query = "SELECT week_id, day FROM menu WHERE id = ?";
        $stmt = mysqli_prepare($conn, $menu_query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $menu = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        mysqli_begin_transaction($conn);
        try {
            // Delete menu
            $query = "DELETE FROM menu WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Delete associated image
            if ($menu) {
                $query = "DELETE FROM menu_images WHERE week_id = ? AND day = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "is", $menu['week_id'], $menu['day']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($conn);
            echo "<script>alert('Menu deleted successfully!');window.location.href = '../update-menu.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Error deleting menu!');window.location.href = '../update-menu.php';</script>";
        }
    }
}

mysqli_close($conn);
?>
