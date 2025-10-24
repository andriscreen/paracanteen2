<?php
session_start(); // Tambahkan ini di paling atas
include "../config/db.php";
include "../../auth.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Tambah/Update menu
    if ($action === 'add') {
        $week_id = intval($_POST['week_id']);
        $vendor_id = intval($_POST['vendor_id']);
        $day = mysqli_real_escape_string($conn, $_POST['day']);
        $menu_name = mysqli_real_escape_string($conn, $_POST['menu_name']);
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Cek apakah menu sudah ada untuk week, vendor, dan day ini
            $check_query = "SELECT id FROM menu WHERE week_id = ? AND vendor_id = ? AND day = ?";
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, "iis", $week_id, $vendor_id, $day);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $existing_menu = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($existing_menu) {
                // Update menu yang existing
                $query = "UPDATE menu SET menu_name = ?, keterangan = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssi", $menu_name, $keterangan, $existing_menu['id']);
                mysqli_stmt_execute($stmt);
                $menu_id = $existing_menu['id'];
                mysqli_stmt_close($stmt);
                $_SESSION['success'] = "✅ Menu berhasil diupdate!";
            } else {
                // Insert menu baru
                $query = "INSERT INTO menu (week_id, vendor_id, day, menu_name, keterangan) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "iisss", $week_id, $vendor_id, $day, $menu_name, $keterangan);
                mysqli_stmt_execute($stmt);
                $menu_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                $_SESSION['success'] = "✅ Menu berhasil ditambahkan!";
            }

            // Handle image upload
            if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] == 0) {
                $file = $_FILES['menu_image'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($ext, $allowed_ext)) {
                    $filename = "week{$week_id}_vendor{$vendor_id}_{$day}.$ext";
                    $target_path = "../../assets/img/menu/" . $filename;

                    // Create directory if it doesn't exist
                    if (!file_exists("../../assets/img/menu/")) {
                        mkdir("../../assets/img/menu/", 0777, true);
                    }

                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $image_url = "assets/img/menu/" . $filename;
                        
                        // Delete existing image jika ada
                        $delete_image = "DELETE FROM menu_images WHERE week_id = ? AND vendor_id = ? AND day = ?";
                        $stmt = mysqli_prepare($conn, $delete_image);
                        mysqli_stmt_bind_param($stmt, "iis", $week_id, $vendor_id, $day);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        
                        // Insert new image
                        $query = "INSERT INTO menu_images (week_id, vendor_id, day, image_url) VALUES (?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "iiss", $week_id, $vendor_id, $day, $image_url);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                }
            }

            mysqli_commit($conn);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "❌ Error: " . mysqli_error($conn);
        }

        header("Location: ../update-menu.php");
        exit;
    }
}

// Handle delete action - PERBAIKAN BESAR DI SINI
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    mysqli_begin_transaction($conn);
    try {
        // Get menu info for image deletion
        $query = "SELECT week_id, vendor_id, day FROM menu WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $menu = mysqli_fetch_assoc($result); // PERBAIKAN: fetch_assoc() bukan fetch_assoc($stmt)
        mysqli_stmt_close($stmt);

        if ($menu) {
            // Delete image record
            $delete_image = "DELETE FROM menu_images WHERE week_id = ? AND vendor_id = ? AND day = ?";
            $stmt = mysqli_prepare($conn, $delete_image);
            mysqli_stmt_bind_param($stmt, "iis", $menu['week_id'], $menu['vendor_id'], $menu['day']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Delete menu
        $query = "DELETE FROM menu WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $affected_rows = mysqli_affected_rows($conn);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        
        if ($affected_rows > 0) {
            $_SESSION['success'] = "🗑️ Menu berhasil dihapus!";
        } else {
            $_SESSION['error'] = "❌ Menu tidak ditemukan!";
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "❌ Error menghapus menu: " . mysqli_error($conn);
    }

    header("Location: ../update-menu.php");
    exit;
}
?>