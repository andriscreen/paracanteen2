<?php
include '../config/db.php';

$week_id = $_GET['week_id'] ?? '';
$vendor_id = $_GET['vendor_id'] ?? '';

if ($week_id && $vendor_id) {
    $query = "SELECT * FROM menu 
              WHERE week_id = ? AND vendor_id = ? 
              ORDER BY FIELD(day, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $week_id, $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $menus = [];
    while ($row = $result->fetch_assoc()) {
        $menus[] = $row;
    }
    
    echo json_encode($menus);
} else {
    echo json_encode([]);
}
?>