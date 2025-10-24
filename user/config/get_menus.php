<?php
include 'db.php';
$week_id = isset($_GET['week_id']) ? intval($_GET['week_id']) : 0;
$menus = [];
if ($week_id) {
  $res = $conn->query("SELECT * FROM menu WHERE week_id = $week_id ORDER BY FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
  while ($row = $res->fetch_assoc()) {
    $menus[] = $row;
  }
}
header('Content-Type: application/json');
echo json_encode($menus);
?>
