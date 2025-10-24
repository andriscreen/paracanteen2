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
    $departemen = mysqli_real_escape_string($conn, $_POST['departemen'] ?? '');  // pastikan ini sesuai dengan tipe data di DB
    $total_kupon = intval($_POST['total_kupon'] ?? 0);  // total kupon diasumsikan integer

    // Validasi input dasar
    if (empty($nama) || empty($nip) || empty($gmail) || empty($password) || empty($departemen)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }

    // Simpan ke database
    $query = "
        INSERT INTO users (nama, nip, gmail, password, departemen, total_kupon, created_at)
        VALUES ('$nama', '$nip', '$gmail', MD5('$password'), '$departemen', $total_kupon, NOW())
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
    $departemen   = mysqli_real_escape_string($conn, $_POST['departemen'] ?? '');  // perhatikan ini
    $total_kupon  = intval($_POST['total_kupon'] ?? 0);

    // Validasi untuk kolom yang wajib diupdate
    if (empty($nip) && empty($nama) && empty($gmail)) {
        echo "<script>alert('At least one of Name, NIP, or Gmail should be provided for update!'); window.history.back();</script>";
        exit;
    }

    // Siapkan query update yang fleksibel
    $update_parts = [];

    // Update `nama` jika ada perubahan
    if (!empty($nama)) {
        $update_parts[] = "nama = '$nama'";
    }

    // Update `nip` jika ada perubahan
    if (!empty($nip)) {
        $update_parts[] = "nip = '$nip'";
    }

    // Update `gmail` jika ada perubahan
    if (!empty($gmail)) {
        $update_parts[] = "gmail = '$gmail'";
    }

    // Update `departemen` jika ada perubahan
    if (!empty($departemen)) {
        $update_parts[] = "departemen = '$departemen'";
    }

    // Update `total_kupon` jika ada perubahan
    if ($total_kupon !== null) {
        $update_parts[] = "total_kupon = $total_kupon";
    }

    // Update `password` jika ada perubahan
    if (!empty($password)) {
        $update_parts[] = "password = MD5('$password')";
    }

    // Jika ada perubahan, gabungkan semua update
    if (!empty($update_parts)) {
        $update_query = "UPDATE users SET " . implode(", ", $update_parts) . ", updated_at = NOW() WHERE id = $id";
        
        // Eksekusi query
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('User updated successfully!'); window.location.href='../update-user.php';</script>";
        } else {
            echo 'Database Error: ' . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('No changes detected!'); window.history.back();</script>";
    }
}

else {
    echo "<script>alert('Invalid action!'); window.history.back();</script>";
}

mysqli_close($conn);
?>
