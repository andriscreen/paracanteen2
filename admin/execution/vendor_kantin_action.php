<?php
include '../config/db.php'; // Pastikan file ini membuat koneksi ke $conn (mysqli_connect)

// Ambil aksi (add / delete)
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    // Ambil data dan amankan input
    $nama       = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $nama_vendor = mysqli_real_escape_string($conn, $_POST['nama_vendor'] ?? ''); // perhatikan nama field di form
    $gmail      = mysqli_real_escape_string($conn, $_POST['gmail'] ?? '');
    $password   = mysqli_real_escape_string($conn, $_POST['password'] ?? '');

    // Validasi input dasar
    if (empty($nama) || empty($nama_vendor) || empty($gmail) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }

    // Simpan ke database
    $query = "
        INSERT INTO vendorkantin (nama, nama_vendor, gmail, password, created_at)
        VALUES ('$nama', '$nama_vendor', '$gmail', MD5('$password'), NOW())
    ";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Account added successfully!'); window.location.href='../manage-vendor-account.php';</script>";
    } else {
        echo 'Database Error: ' . mysqli_error($conn);
    }
}

elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        $query = "DELETE FROM vendorkantin WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Account deleted successfully!'); window.location.href='../manage-vendor-account.php';</script>";
        } else {
            echo 'Database Error: ' . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Invalid ID!'); window.history.back();</script>";
    }
}

else {
    echo "<script>alert('Invalid action!'); window.history.back();</script>";
}

mysqli_close($conn);
?>
