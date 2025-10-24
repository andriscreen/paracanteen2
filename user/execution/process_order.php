<?php
session_start();

// Pastikan Anda meng-include file db.php untuk koneksi
require_once '../config/db.php';  // Sesuaikan path jika perlu

// Periksa apakah user_id ada di session
if (!isset($_SESSION['user_id'])) {
    // Redirect ke halaman login jika user_id tidak ada
    header("Location: ../../form_login.php");
    exit;
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $week_id = $_POST['week'];  // ID Week yang dipilih
    $year_id = $_POST['year'];  // ID Year yang dipilih
    $plant_id = $_POST['plant']; // ID Plant yang dipilih
    $place_id = $_POST['place']; // ID Place yang dipilih
    $shift_id = $_POST['shift']; // ID Shift yang dipilih
    
    // Ambil data untuk setiap hari
    $makan_senin = isset($_POST['makan_senin']) ? 1 : 0;
    $kupon_senin = isset($_POST['kupon_senin']) ? 1 : 0;
    $libur_senin = isset($_POST['libur_senin']) ? 1 : 0;
    
    $makan_selasa = isset($_POST['makan_selasa']) ? 1 : 0;
    $kupon_selasa = isset($_POST['kupon_selasa']) ? 1 : 0;
    $libur_selasa = isset($_POST['libur_selasa']) ? 1 : 0;
    
    $makan_rabu = isset($_POST['makan_rabu']) ? 1 : 0;
    $kupon_rabu = isset($_POST['kupon_rabu']) ? 1 : 0;
    $libur_rabu = isset($_POST['libur_rabu']) ? 1 : 0;
    
    $makan_kamis = isset($_POST['makan_kamis']) ? 1 : 0;
    $kupon_kamis = isset($_POST['kupon_kamis']) ? 1 : 0;
    $libur_kamis = isset($_POST['libur_kamis']) ? 1 : 0;
    
    $makan_jumat = isset($_POST['makan_jumat']) ? 1 : 0;
    $kupon_jumat = isset($_POST['kupon_jumat']) ? 1 : 0;
    $libur_jumat = isset($_POST['libur_jumat']) ? 1 : 0;
    
    $makan_sabtu = isset($_POST['makan_sabtu']) ? 1 : 0;
    $kupon_sabtu = isset($_POST['kupon_sabtu']) ? 1 : 0;
    $libur_sabtu = isset($_POST['libur_sabtu']) ? 1 : 0;
    
    $makan_minggu = isset($_POST['makan_minggu']) ? 1 : 0;
    $kupon_minggu = isset($_POST['kupon_minggu']) ? 1 : 0;
    $libur_minggu = isset($_POST['libur_minggu']) ? 1 : 0;

    // Mulai transaksi untuk memastikan atomisitas
    $conn->begin_transaction();

    try {
        // 1. Insert data ke tabel orders dengan semua kolom hari
        $stmt = $conn->prepare("INSERT INTO orders (
            week_id, year_id, plant_id, place_id, shift_id, user_id,
            makan_senin, kupon_senin, libur_senin,
            makan_selasa, kupon_selasa, libur_selasa,
            makan_rabu, kupon_rabu, libur_rabu,
            makan_kamis, kupon_kamis, libur_kamis,
            makan_jumat, kupon_jumat, libur_jumat,
            makan_sabtu, kupon_sabtu, libur_sabtu,
            makan_minggu, kupon_minggu, libur_minggu
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "iiiiiiiiiiiiiiiiiiiiiiiiiii", 
            $week_id, $year_id, $plant_id, $place_id, $shift_id, $user_id,
            $makan_senin, $kupon_senin, $libur_senin,
            $makan_selasa, $kupon_selasa, $libur_selasa,
            $makan_rabu, $kupon_rabu, $libur_rabu,
            $makan_kamis, $kupon_kamis, $libur_kamis,
            $makan_jumat, $kupon_jumat, $libur_jumat,
            $makan_sabtu, $kupon_sabtu, $libur_sabtu,
            $makan_minggu, $kupon_minggu, $libur_minggu
        );
        
        $stmt->execute();
        
        // Ambil ID order yang baru saja disimpan
        $order_id = $conn->insert_id;
        
        // Hitung total kupon untuk trigger (jika masih menggunakan trigger lama)
        $total_kupon = $kupon_senin + $kupon_selasa + $kupon_rabu + $kupon_kamis + 
                      $kupon_jumat + $kupon_sabtu + $kupon_minggu;
        
        // Jika menggunakan trigger baru, tidak perlu manual insert ke kupon_history
        // Trigger akan otomatis menangani ini
        
        // Commit transaksi
        $conn->commit();
        
        // Redirect setelah berhasil
        header("Location: ../history.php");
        exit(); // Jangan lupa untuk menghentikan eksekusi script setelah redirect
        
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $conn->rollback();
        echo "Terjadi kesalahan: " . $e->getMessage();
        // Untuk debugging, Anda bisa tambahkan:
        // error_log("Error in process_order.php: " . $e->getMessage());
    }

    // Menutup statement dan koneksi
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
} else {
    // Jika bukan POST request, redirect ke halaman order
    header("Location: ../food-order.php");
    exit();
}
?>