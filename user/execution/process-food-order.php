<?php
require_once 'config/db.php';
require_once 'config/kupon_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses order makanan yang sudah ada
    // ...
    
    // Setelah order berhasil disimpan, tambahkan kupon
    // Asumsi setiap pemesanan mendapat 1 kupon
    $jumlahKupon = 1;
    $keterangan = "Kupon dari pemesanan makanan #" . $orderId;
    
    if (tambahKupon($_SESSION['user_id'], $orderId, $jumlahKupon, $keterangan)) {
        $_SESSION['success'] = "Pesanan berhasil dan Anda mendapatkan {$jumlahKupon} kupon!";
    } else {
        $_SESSION['warning'] = "Pesanan berhasil tetapi gagal menambahkan kupon.";
    }
    
    header("Location: history.php");
    exit;
}