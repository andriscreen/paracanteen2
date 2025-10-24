<?php
session_start();

// Koneksi ke MySQL
include_once __DIR__ . '/db_master.php';  // Path relatif ke db_master.php

// Ambil data dari form login
$gmail    = $_POST['gmail'];
$password = $_POST['password'];

// Query cek login di semua tabel (termasuk pic_kantin)
$sql = "
SELECT 'user' AS role, id, nama, gmail
FROM users
WHERE gmail = ? AND password = MD5(?)
UNION
SELECT 'vendorkantin' AS role, id, nama, gmail
FROM vendorkantin
WHERE gmail = ? AND password = MD5(?)
UNION
SELECT 'admin' AS role, id, nama, gmail
FROM admin
WHERE gmail = ? AND password = MD5(?)
UNION
SELECT 'pickantin' AS role, id, nama, gmail
FROM pic_kantin
WHERE gmail = ? AND password = MD5(?)
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $gmail, $password, $gmail, $password, $gmail, $password, $gmail, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Simpan session
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['nama']    = $row['nama'];
    $_SESSION['gmail']   = $row['gmail'];
    $_SESSION['role']    = $row['role'];

    // Redirect sesuai role
    switch ($row['role']) {
        case 'user':
            header("Location: user/");
            break;
        case 'vendorkantin':
            header("Location: vendorkantin/");
            break;
        case 'admin':
            header("Location: admin/");
            break;
        case 'pickantin':
            header("Location: pickantin/");
            break;
        default:
            // Redirect ke halaman default jika role tidak dikenali
            header("Location: form_login.php");
            break;
    }
    exit;
} else {
    echo "<script>alert('Login gagal! Gmail atau password salah.'); window.location.href = 'form_login.php';</script>";
    exit;
}
?>