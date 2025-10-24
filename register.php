<?php
session_start();

// Koneksi ke MySQL
include_once __DIR__ . '/db_master.php';  // Path relatif ke db_master.php

// Ambil input dari form
$nama     = trim($_POST['nama']);
$gmail    = trim($_POST['gmail']);
$password = trim($_POST['password']);

// Validasi sederhana
if (empty($nama) || empty($gmail) || empty($password)) {
    die("Semua field wajib diisi.");
}

// Cek apakah gmail sudah terdaftar
$check = $conn->prepare("SELECT id FROM users WHERE gmail = ?");
$check->bind_param("s", $gmail);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Gmail sudah digunakan. Silakan gunakan yang lain.";
    exit;
}
$check->close();

// Simpan user baru (gunakan MD5 sesuai login kamu)
$passwordHash = md5($password);

$stmt = $conn->prepare("INSERT INTO users (nama, gmail, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $gmail, $passwordHash);

if ($stmt->execute()) {
    echo "<script>
        alert('Sign up berhasil. Silakan login.');
        window.location.href = 'form_login.php';
    </script>";
} else {
    $error = addslashes($stmt->error); // escape error biar aman di JS
    echo "<script>
        alert('Gagal sign up: {$error}');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>
