<?php
declare(strict_types=1);
// AJAX endpoint: return JSON list of users matching query
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/db.php';

// only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if ($q === '' || strlen($q) < 1) {
    echo json_encode([]);
    exit;
}

// limit and prepare
$limit = 30;
$like = '%' . $q . '%';

$sql = "SELECT id, nama, gmail FROM users WHERE nama LIKE ? OR gmail LIKE ? ORDER BY nama ASC LIMIT ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('ssi', $like, $like, $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($row = $res->fetch_assoc()) {
        $out[] = ['id' => (int)$row['id'], 'nama' => $row['nama'], 'gmail' => $row['gmail']];
    }
    echo json_encode($out);
    $stmt->close();
    exit;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}
