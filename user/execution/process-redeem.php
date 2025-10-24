<?php
require_once "../../auth.php";
require_once "../config/db.php";
require_once "../config/redeem-items.php";

if ($_SESSION['role'] !== 'user') {
    header("Location: ../../form_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../redeem-kupon.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user's current kupon balance
$query = "SELECT total_kupon FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$current_kupon = $user_data['total_kupon'] ?? 0;

// Calculate total kupon needed
$total_kupon_needed = 0;
$items_to_redeem = [];

foreach ($_POST['qty'] as $item_id => $qty) {
    $qty = intval($qty);
    if ($qty <= 0) continue;
    
    // Find item in our config
    $item = array_filter($items, function($i) use ($item_id) {
        return $i['id'] == $item_id;
    });
    $item = reset($item);
    
    if (!$item) continue;
    
    $kupon_cost = $qty * $item['kupon'];
    $total_kupon_needed += $kupon_cost;
    
    $items_to_redeem[] = [
        'item_id' => $item_id,
        'qty' => $qty,
        'nama' => $_POST['item_name'][$item_id],
        'kupon_cost' => $kupon_cost
    ];
}

// Validate if user has enough kupon
if ($total_kupon_needed > $current_kupon) {
    $_SESSION['error'] = "Kupon Anda tidak mencukupi untuk penukaran ini.";
    header("Location: ../redeem-kupon.php");
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update user's kupon balance
    $new_balance = $current_kupon - $total_kupon_needed;
    $query = "UPDATE users SET total_kupon = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $new_balance, $user_id);
    $stmt->execute();

    // Record redemption in redemption_history table
    $query = "INSERT INTO redemption_history (user_id, item_id, quantity, kupon_used, item_name, redemption_date) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    
    foreach ($items_to_redeem as $item) {
        $stmt->bind_param("iiiis", 
            $user_id, 
            $item['item_id'],
            $item['qty'],
            $item['kupon_cost'],
            $item['nama']
        );
        $stmt->execute();
    }

    $conn->commit();
    $_SESSION['success'] = "Penukaran kupon berhasil! Silakan ambil barang Anda di kantin.";
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Terjadi kesalahan saat memproses penukaran. Silakan coba lagi.";
}

header("Location: ../redeem-kupon.php");