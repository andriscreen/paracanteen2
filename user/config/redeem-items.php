<?php
require_once __DIR__ . '/../../admin/config/db.php';
$items = [];
$sql = "SELECT id, nama, kupon, gambar FROM redeem_items WHERE aktif = 1 ORDER BY id ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}