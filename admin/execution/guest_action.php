<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session untuk alert
session_start();

include '../config/db.php';

// Cek koneksi database
if (!$conn) {
    die("<script>alert('Database connection failed!'); window.history.back();</script>");
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $gmail = mysqli_real_escape_string($conn, $_POST['gmail'] ?? '');
    $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');

    // Validasi input dasar
    if (empty($nama) || empty($gmail) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../manage-guest-account.php");
        exit;
    }

    // Validasi format email
    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: ../manage-guest-account.php");
        exit;
    }

    // Cek apakah gmail sudah terdaftar
    $check_gmail = mysqli_query($conn, "SELECT id FROM guest WHERE gmail = '$gmail'");
    if (!$check_gmail) {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
        header("Location: ../manage-guest-account.php");
        exit;
    }
    
    if (mysqli_num_rows($check_gmail) > 0) {
        $_SESSION['error'] = "Gmail already exists! Please use a different email.";
        header("Location: ../manage-guest-account.php");
        exit;
    }

    // Simpan ke database
    $query = "INSERT INTO guest (nama, gmail, password, created_at) VALUES ('$nama', '$gmail', MD5('$password'), NOW())";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Guest account added successfully!";
        header("Location: ../manage-guest-account.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to add guest account: " . mysqli_error($conn);
        header("Location: ../manage-guest-account.php");
        exit;
    }
}

elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        // Cek dulu apakah guest ada
        $check_guest = mysqli_query($conn, "SELECT id FROM guest WHERE id = $id");
        if (!$check_guest) {
            $_SESSION['error'] = "Database error: " . mysqli_error($conn);
            header("Location: ../manage-guest-account.php");
            exit;
        }
        
        if (mysqli_num_rows($check_guest) == 0) {
            $_SESSION['error'] = "Guest account not found!";
            header("Location: ../manage-guest-account.php");
            exit;
        }

        $query = "DELETE FROM guest WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Guest account deleted successfully!";
            header("Location: ../manage-guest-account.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to delete guest account: " . mysqli_error($conn);
            header("Location: ../manage-guest-account.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid ID!";
        header("Location: ../manage-guest-account.php");
        exit;
    }
}

else {
    $_SESSION['error'] = "Invalid action!";
    header("Location: ../manage-guest-account.php");
    exit;
}

mysqli_close($conn);
?>