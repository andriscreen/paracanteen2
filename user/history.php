<?php
/**
 * Order History Page - Updated for new orders structure
 */

require_once __DIR__ . '/config/db.php';
require_once dirname(__DIR__) . '/auth.php';

if (!isset($_SESSION['role'], $_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../form_login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Helper for safe HTML output
if (!function_exists('e')) {
    function e($v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Helper to bind dynamic params using call_user_func_array with references
if (!function_exists('stmt_bind_params_dyn')) {
    function stmt_bind_params_dyn(mysqli_stmt $stmt, string $types, array &$params): bool
    {
        $bind = [$types];
        foreach ($params as $k => $v) {
            $bind[] = &$params[$k];
        }
        return call_user_func_array([$stmt, 'bind_param'], $bind);
    }
}

// Pagination parameters
$defaultPerPage = 20;
$maxPerPage = 100;
$perPage = isset($_GET['per_page']) ? max(1, min((int) $_GET['per_page'], $maxPerPage)) : $defaultPerPage;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// Filters (validated)
$conditions = ['o.user_id = ?'];
$types = 'i';
$params = [$user_id];

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

if ($start_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
    $conditions[] = 'o.created_at >= ?';
    $params[] = $start_date . ' 00:00:00';
    $types .= 's';
}
if ($end_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
    $conditions[] = 'o.created_at <= ?';
    $params[] = $end_date . ' 23:59:59';
    $types .= 's';
}

$shift = isset($_GET['shift']) ? (int) $_GET['shift'] : 0;
if ($shift > 0) {
    $conditions[] = 'o.shift_id = ?';
    $params[] = $shift;
    $types .= 'i';
}

$week = isset($_GET['week']) ? (int) $_GET['week'] : 0;
if ($week > 0) {
    $conditions[] = 'o.week_id = ?';
    $params[] = $week;
    $types .= 'i';
}

$where = 'WHERE ' . implode(' AND ', $conditions);

// Count total rows for consistent pagination
$countSql = "SELECT COUNT(*) AS total
FROM `orders` o
JOIN `year` y ON o.year_id = y.id
JOIN `week` w ON o.week_id = w.id
JOIN `plant` p ON o.plant_id = p.id
JOIN `place` pl ON o.place_id = pl.id
LEFT JOIN `shift` s ON o.shift_id = s.id
$where";

$countStmt = $conn->prepare($countSql);
if (!$countStmt) {
    http_response_code(500);
    exit('Database error (prepare count): ' . e($conn->error));
}
if (!stmt_bind_params_dyn($countStmt, $types, $params)) {
    http_response_code(500);
    exit('Database error (bind_param count): ' . e($countStmt->error));
}
if (!$countStmt->execute()) {
    http_response_code(500);
    exit('Database error (execute count): ' . e($countStmt->error));
}
$total = 0;
if (method_exists($countStmt, 'get_result')) {
    $cntRes = $countStmt->get_result();
    if ($cntRes instanceof mysqli_result) {
        $row = $cntRes->fetch_assoc();
        $total = isset($row['total']) ? (int) $row['total'] : 0;
        $cntRes->free();
    }
} else {
    if (!$countStmt->bind_result($total)) {
        http_response_code(500);
        exit('Database error (bind_result count): ' . e($countStmt->error));
    }
    $countStmt->fetch();
}
$countStmt->close();

$totalPages = max(1, (int) ceil($total / $perPage));
if ($total > 0 && $page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

// Data query (paginated) - UPDATED for new structure
$selectSql = "SELECT 
    o.id AS order_id,
    y.year_value,
    w.week_number,
    p.name AS plant_name,
    pl.name AS place_name,
    s.nama_shift AS shift_name,
    o.created_at,
    -- Monday
    o.makan_senin, o.kupon_senin, o.libur_senin,
    -- Tuesday
    o.makan_selasa, o.kupon_selasa, o.libur_selasa,
    -- Wednesday
    o.makan_rabu, o.kupon_rabu, o.libur_rabu,
    -- Thursday
    o.makan_kamis, o.kupon_kamis, o.libur_kamis,
    -- Friday
    o.makan_jumat, o.kupon_jumat, o.libur_jumat,
    -- Saturday
    o.makan_sabtu, o.kupon_sabtu, o.libur_sabtu,
    -- Sunday
    o.makan_minggu, o.kupon_minggu, o.libur_minggu
FROM `orders` o
JOIN `year` y ON o.year_id = y.id
JOIN `week` w ON o.week_id = w.id
JOIN `plant` p ON o.plant_id = p.id
JOIN `place` pl ON o.place_id = pl.id
LEFT JOIN `shift` s ON o.shift_id = s.id
$where
ORDER BY 
  y.year_value DESC,
  w.week_number DESC,
  o.created_at DESC,
  o.id DESC
LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

$stmt = $conn->prepare($selectSql);
if (!$stmt) {
    http_response_code(500);
    exit('Database error (prepare): ' . e($conn->error));
}

// Bind the same dynamic params as used for count
if (!stmt_bind_params_dyn($stmt, $types, $params)) {
    http_response_code(500);
    exit('Database error (bind_param): ' . e($stmt->error));
}

if (!$stmt->execute()) {
    http_response_code(500);
    exit('Database error (execute): ' . e($stmt->error));
}

// Fetch results using mysqlnd if available; otherwise, use bind_result fallback
$rows = [];
if (method_exists($stmt, 'get_result')) {
    $result = $stmt->get_result();
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }
} else {
    // Bind all columns for fallback
    $bindParams = [
        &$order_id, &$year_value, &$week_number, &$plant_name, &$place_name, &$shift_name, &$created_at,
        &$makan_senin, &$kupon_senin, &$libur_senin,
        &$makan_selasa, &$kupon_selasa, &$libur_selasa,
        &$makan_rabu, &$kupon_rabu, &$libur_rabu,
        &$makan_kamis, &$kupon_kamis, &$libur_kamis,
        &$makan_jumat, &$kupon_jumat, &$libur_jumat,
        &$makan_sabtu, &$kupon_sabtu, &$libur_sabtu,
        &$makan_minggu, &$kupon_minggu, &$libur_minggu
    ];
    
    if (!$stmt->bind_result(...$bindParams)) {
        http_response_code(500);
        exit('Database error (bind_result): ' . e($stmt->error));
    }
    
    while ($stmt->fetch()) {
        $rows[] = [
            'order_id' => $order_id,
            'year_value' => $year_value,
            'week_number' => $week_number,
            'plant_name' => $plant_name,
            'place_name' => $place_name,
            'shift_name' => $shift_name,
            'created_at' => $created_at,
            // Monday
            'makan_senin' => $makan_senin, 'kupon_senin' => $kupon_senin, 'libur_senin' => $libur_senin,
            // Tuesday
            'makan_selasa' => $makan_selasa, 'kupon_selasa' => $kupon_selasa, 'libur_selasa' => $libur_selasa,
            // Wednesday
            'makan_rabu' => $makan_rabu, 'kupon_rabu' => $kupon_rabu, 'libur_rabu' => $libur_rabu,
            // Thursday
            'makan_kamis' => $makan_kamis, 'kupon_kamis' => $kupon_kamis, 'libur_kamis' => $libur_kamis,
            // Friday
            'makan_jumat' => $makan_jumat, 'kupon_jumat' => $kupon_jumat, 'libur_jumat' => $libur_jumat,
            // Saturday
            'makan_sabtu' => $makan_sabtu, 'kupon_sabtu' => $kupon_sabtu, 'libur_sabtu' => $libur_sabtu,
            // Sunday
            'makan_minggu' => $makan_minggu, 'kupon_minggu' => $kupon_minggu, 'libur_minggu' => $libur_minggu
        ];
    }
}

$stmt->close();

// Function to get day information from order
function getDayInfo($order, $day) {
    $dayLower = strtolower($day);
    $makan = $order["makan_{$dayLower}"] ?? 0;
    $kupon = $order["kupon_{$dayLower}"] ?? 0;
    $libur = $order["libur_{$dayLower}"] ?? 0;
    
    $info = [];
    if ($makan == 1) $info[] = 'Makan';
    if ($kupon == 1) $info[] = 'Kupon';
    if ($libur == 1) $info[] = 'Libur';
    
    return [
        'day' => $day,
        'info' => $info ? implode(', ', $info) : '-'
    ];
}

// Prepare data for display - convert orders to day-based rows
$displayRows = [];
$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

foreach ($rows as $order) {
    foreach ($days as $day) {
        $dayInfo = getDayInfo($order, $day);
        // Only add row if there's some information (not all empty)
        if ($dayInfo['info'] !== '-') {
            $displayRows[] = [
                'order_id' => $order['order_id'],
                'year_value' => $order['year_value'],
                'week_number' => $order['week_number'],
                'plant_name' => $order['plant_name'],
                'place_name' => $order['place_name'],
                'shift_name' => $order['shift_name'],
                'created_at' => $order['created_at'],
                'day' => $dayInfo['day'],
                'info' => $dayInfo['info']
            ];
        }
    }
}

// Helpers for pagination UI
$basePath = basename($_SERVER['PHP_SELF']);
$qs = function (int $p) use ($perPage): string {
    $params = $_GET;
    $params['page'] = $p;
    $params['per_page'] = $perPage;
    return '?' . http_build_query($params);
};
$from = $total === 0 ? 0 : ($offset + 1);
$to = $total === 0 ? 0 : min($offset + $perPage, $total);
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Order History | ParaCanteen</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
    <!-- Bootstrap Icons for clock-history icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    <style>
      .pagination {
        margin-bottom: 0;
      }
      .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 0.25rem;
        margin: 0 2px;
      }
      .page-link:focus {
        box-shadow: none;
      }
      .pagination .bx {
        font-size: 1.1rem;
        line-height: 1;
        vertical-align: middle;
      }
      .page-item.active .page-link {
        background-color: #696cff;
        border-color: #696cff;
      }
    </style>
  </head>
  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include 'layout/sidebar.php'; ?>
        <div class="layout-page">
          <?php include 'layout/navbar.php'; ?>
          <div class="container mt-4">
            <div class="card shadow-sm p-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="bi bi-clock-history"></i> Order History</h4>
                <div class="text-muted small">
                  Showing <?= e($from) ?>–<?= e($to) ?> of <?= e($total) ?> orders
                </div>
              </div>
              <div class="mb-3">
                <form method="get" class="row g-3 mb-3">
                  <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= e($_GET['start_date'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= e($_GET['end_date'] ?? '') ?>">
                  </div>
                  <div class="col-md-2">
                    <label for="shift" class="form-label">Shift</label>
                    <select class="form-select" id="shift" name="shift">
                      <option value="">All</option>
                      <?php
                      if ($shiftRes = $conn->query("SELECT `id`, `nama_shift` FROM `shift`")) {
                          while ($s = $shiftRes->fetch_assoc()) {
                              $sid = (int) $s['id'];
                              $selected = (isset($_GET['shift']) && (int) $_GET['shift'] === $sid) ? ' selected' : '';
                              echo '<option value="' . $sid . '"' . $selected . '>' . e($s['nama_shift']) . '</option>';
                          }
                          $shiftRes->free();
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label for="week" class="form-label">Week</label>
                    <select class="form-select" id="week" name="week">
                      <option value="">All</option>
                      <?php
                      if ($weekRes = $conn->query("SELECT `id`, `week_number` FROM `week` ORDER BY `week_number` ASC")) {
                          while ($w = $weekRes->fetch_assoc()) {
                              $wid = (int) $w['id'];
                              $selected = (isset($_GET['week']) && (int) $_GET['week'] === $wid) ? ' selected' : '';
                              echo '<option value="' . $wid . '"' . $selected . '>Week ' . e($w['week_number']) . '</option>';
                          }
                          $weekRes->free();
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                  </div>
                </form>
              </div>
                <div class="table-responsive">
                  <table class="table table-bordered align-middle mb-3">
                    <thead class="table-light">
                      <tr>
                        <th>#</th>
                        <th>Year</th>
                        <th>Week</th>
                        <th>Plant</th>
                        <th>Place</th>
                        <th>Day</th>
                        <th>Shift</th>
                        <th>Information</th>
                        <th>Date Ordered</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php if (count($displayRows) === 0): ?>
                      <tr>
                        <td colspan="9" class="text-center">No order history found.</td>
                      </tr>
                    <?php else: ?>
                      <?php $no = $offset + 1; ?>
                      <?php foreach ($displayRows as $r): ?>
                        <tr>
                          <td><?= e($no++) ?></td>
                          <td><?= e($r['year_value']) ?></td>
                          <td><?= e($r['week_number']) ?></td>
                          <td><?= e($r['plant_name']) ?></td>
                          <td><?= e($r['place_name']) ?></td>
                          <td><?= e($r['day']) ?></td>
                          <td><?= e($r['shift_name']) ?></td>
                          <td><?= e($r['info']) ?></td>
                          <td><?= e(!empty($r['created_at']) ? date('Y-m-d H:i', strtotime($r['created_at'])) : '-') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <?php if ($totalPages > 1): ?>
              <hr class="my-4">
              <div class="d-flex justify-content-between align-items-center px-2">
                <div class="small text-muted">
                  Halaman <?= e($page) ?> dari <?= e($totalPages) ?>
                </div>
                <nav aria-label="Order history pagination">
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                      <a class="page-link" href="<?= e($basePath . $qs(max(1, $page - 1))) ?>" tabindex="-1">
                        <i class="bx bx-chevron-left"></i> Previous
                      </a>
                    </li>
                    <?php if ($totalPages <= 5): ?>
                      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                          <a class="page-link" href="<?= e($basePath . $qs($i)) ?>"><?= e($i) ?></a>
                        </li>
                      <?php endfor; ?>
                    <?php else: ?>
                      <?php
                      $start = max(1, min($page - 2, $totalPages - 4));
                      $end = min($totalPages, max($page + 2, 5));
                      if ($start > 1): ?>
                        <li class="page-item">
                          <a class="page-link" href="<?= e($basePath . $qs(1)) ?>">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                          <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                      <?php endif; ?>
                      <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                          <a class="page-link" href="<?= e($basePath . $qs($i)) ?>"><?= e($i) ?></a>
                        </li>
                      <?php endfor; ?>
                      <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                          <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                          <a class="page-link" href="<?= e($basePath . $qs($totalPages)) ?>"><?= e($totalPages) ?></a>
                        </li>
                      <?php endif; ?>
                    <?php endif; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                      <a class="page-link" href="<?= e($basePath . $qs(min($totalPages, $page + 1))) ?>">
                        Next <i class="bx bx-chevron-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboards-analytics.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>