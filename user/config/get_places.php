<?php
include 'db.php';
$plant_id = isset($_GET['plant_id']) ? intval($_GET['plant_id']) : 0;
$places = [];
if ($plant_id) {
  $res = $conn->query("SELECT id, name FROM place WHERE plant_id = $plant_id ORDER BY name ASC");
  while ($row = $res->fetch_assoc()) {
    $places[] = $row;
  }
}
header('Content-Type: application/json');
echo json_encode($places);
?>
