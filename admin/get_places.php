<?php
include 'config/db.php';

header('Content-Type: application/json');

if (isset($_GET['plant_id'])) {
    $plant_id = (int)$_GET['plant_id'];
    $query = "SELECT * FROM place WHERE plant_id = ? ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $plant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $places = [];
    while ($place = $result->fetch_assoc()) {
        $places[] = $place;
    }
    
    echo json_encode($places);
} else {
    echo json_encode([]);
}
?>