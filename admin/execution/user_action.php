<?php
include '../config/db.php'; // Pastikan file ini membuat koneksi ke $conn (mysqli_connect)

// Ambil aksi (add / delete / update)
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    // Ambil data dan amankan input
    $nama       = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $nip        = mysqli_real_escape_string($conn, $_POST['nip'] ?? '');
    $gmail      = mysqli_real_escape_string($conn, $_POST['gmail'] ?? '');
    $password   = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
    $rfid       = mysqli_real_escape_string($conn, $_POST['rfid'] ?? '');
    $departemen = mysqli_real_escape_string($conn, $_POST['departemen'] ?? '');
    $total_kupon = intval($_POST['total_kupon'] ?? 0);

    // Validasi input dasar
    if (empty($nama) || empty($nip) || empty($gmail) || empty($password) || empty($departemen)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah RFID sudah digunakan (jika diisi)
    if (!empty($rfid)) {
        $check_rfid = mysqli_query($conn, "SELECT id FROM users WHERE rfid = '$rfid'");
        if (mysqli_num_rows($check_rfid) > 0) {
            echo "<script>alert('RFID already exists! Please use a different RFID.'); window.history.back();</script>";
            exit;
        }
    }

    // Simpan ke database
    $query = "
        INSERT INTO users (nama, nip, gmail, password, rfid, departemen, total_kupon, created_at)
        VALUES ('$nama', '$nip', '$gmail', MD5('$password'), " . (empty($rfid) ? "NULL" : "'$rfid'") . ", '$departemen', $total_kupon, NOW())
    ";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Account added successfully!'); window.location.href='../manage-user-account.php';</script>";
    } else {
        echo 'Database Error: ' . mysqli_error($conn);
    }
}

elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        $query = "DELETE FROM users WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Account deleted successfully!'); window.location.href='../manage-user-account.php';</script>";
        } else {
            echo 'Database Error: ' . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Invalid ID!'); window.history.back();</script>";
    }
}

elseif ($action === 'update') {
    // Ambil data dan amankan input untuk update
    $id           = intval($_POST['id'] ?? 0);
    $nama         = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $nip          = mysqli_real_escape_string($conn, $_POST['nip'] ?? '');
    $gmail        = mysqli_real_escape_string($conn, $_POST['gmail'] ?? '');
    $password     = mysqli_real_escape_string($conn, $_POST['password'] ?? '');
    $rfid         = mysqli_real_escape_string($conn, $_POST['rfid'] ?? '');
    $departemen   = mysqli_real_escape_string($conn, $_POST['departemen'] ?? '');
    $total_kupon  = intval($_POST['total_kupon'] ?? 0);

    // Validasi untuk kolom yang wajib diupdate
    if (empty($nama) || empty($nip) || empty($gmail) || empty($departemen)) {
        echo "<script>alert('Name, NIP, Gmail, and Department are required fields!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah RFID sudah digunakan oleh user lain (jika diisi)
    if (!empty($rfid)) {
        $check_rfid = mysqli_query($conn, "SELECT id FROM users WHERE rfid = '$rfid' AND id != $id");
        if (mysqli_num_rows($check_rfid) > 0) {
            echo "<script>alert('RFID already exists! Please use a different RFID.'); window.history.back();</script>";
            exit;
        }
    }

    // Siapkan query update yang fleksibel
    $update_parts = [];

    // Update field yang wajib
    $update_parts[] = "nama = '$nama'";
    $update_parts[] = "nip = '$nip'";
    $update_parts[] = "gmail = '$gmail'";
    $update_parts[] = "departemen = '$departemen'";
    $update_parts[] = "total_kupon = $total_kupon";

    // Update RFID (bisa NULL jika kosong)
    if (!empty($rfid)) {
        $update_parts[] = "rfid = '$rfid'";
    } else {
        $update_parts[] = "rfid = NULL";
    }

    // Update `password` jika ada perubahan
    if (!empty($password)) {
        $update_parts[] = "password = MD5('$password')";
    }

    // Gabungkan semua update
    $update_query = "UPDATE users SET " . implode(", ", $update_parts) . ", updated_at = NOW() WHERE id = $id";
    
    // Eksekusi query
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('User updated successfully!'); window.location.href='../update-user.php';</script>";
    } else {
        echo 'Database Error: ' . mysqli_error($conn);
    }
}

else {
    echo "<script>alert('Invalid action!'); window.history.back();</script>";
}

mysqli_close($conn);
?>