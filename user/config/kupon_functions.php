<?php
require_once 'db.php';

function tambahKupon($userId, $orderId, $jumlahKupon, $keterangan = '') {
    global $conn;
    
    try {
        // Mulai transaksi
        $conn->begin_transaction();
        
        // Insert ke kupon_history
        $query = "INSERT INTO kupon_history (user_id, order_id, jumlah_kupon, keterangan) 
                 VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiis", $userId, $orderId, $jumlahKupon, $keterangan);
        $stmt->execute();
        
        // Commit transaksi
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        error_log("Error adding kupon: " . $e->getMessage());
        return false;
    }
}

function getKuponBalance($userId) {
    global $conn;
    
    $query = "SELECT total_kupon FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['total_kupon'];
    }
    
    return 0;
}

function getKuponHistory($userId, $limit = 10) {
    global $conn;
    
    $query = "SELECT kh.*, fo.order_date 
              FROM kupon_history kh
              JOIN food_orders fo ON kh.order_id = fo.id
              WHERE kh.user_id = ?
              ORDER BY kh.tanggal_dapat DESC
              LIMIT ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}