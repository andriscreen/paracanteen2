<?php
include "../config/db.php";
include "../../auth.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['week_id'])) {
    $week_id = mysqli_real_escape_string($conn, $_GET['week_id']);
    
    $query = "SELECT id, day, menu_name FROM menu WHERE week_id = ? ORDER BY 
              CASE day 
                WHEN 'Senin' THEN 1 
                WHEN 'Selasa' THEN 2 
                WHEN 'Rabu' THEN 3 
                WHEN 'Kamis' THEN 4 
                WHEN 'Jumat' THEN 5 
                WHEN 'Sabtu' THEN 6 
                WHEN 'Minggu' THEN 7 
              END";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $week_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $menus = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $menus[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($menus);
}

mysqli_close($conn);
?>