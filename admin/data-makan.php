<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../form_login.php");
    exit;
}

include 'config/db.php';

// Set locale untuk bahasa Indonesia
$conn->query("SET lc_time_names = 'id_ID'");

// Ambil parameter filter
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$selected_week = isset($_GET['week_id']) ? (int)$_GET['week_id'] : 0;
$selected_plant = isset($_GET['plant_id']) ? (int)$_GET['plant_id'] : 0;
$selected_place = isset($_GET['place_id']) ? (int)$_GET['place_id'] : 0;
$selected_shift = isset($_GET['shift_id']) ? (int)$_GET['shift_id'] : 0;
$selected_day = isset($_GET['day']) ? $_GET['day'] : '';

// Ambil hari dari tanggal yang dipilih
$hari_result = $conn->query("SELECT DAYNAME('$selected_date') as hari");
$hari_row = $hari_result->fetch_assoc();
$hari = $hari_row['hari'];

// Konversi ke format Indonesia
$hari_indonesia = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$hari_ini = $hari_indonesia[$hari] ?? $hari;

// Ambil daftar filter
$weeks_result = $conn->query("
    SELECT DISTINCT w.id, w.week_number 
    FROM week w 
    JOIN orders o ON w.id = o.week_id 
    ORDER BY w.week_number DESC
");

$plants_result = $conn->query("SELECT id, name FROM plant ORDER BY name");
$places_result = $conn->query("SELECT id, name FROM place ORDER BY name");
$shifts_result = $conn->query("SELECT id, nama_shift FROM shift ORDER BY id");

// Query data order berdasarkan filter
$sql = "SELECT 
            o.*, 
            u.nama, 
            u.nip, 
            u.departemen,
            p.name as plant_name,
            pl.name as place_name,
            s.nama_shift as shift_name,
            w.week_number
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        JOIN plant p ON o.plant_id = p.id 
        JOIN place pl ON o.place_id = pl.id 
        LEFT JOIN shift s ON o.shift_id = s.id 
        JOIN week w ON o.week_id = w.id 
        WHERE 1=1";

$params = [];
$types = "";

if ($selected_week > 0) {
    $sql .= " AND o.week_id = ?";
    $params[] = $selected_week;
    $types .= "i";
}

if ($selected_plant > 0) {
    $sql .= " AND o.plant_id = ?";
    $params[] = $selected_plant;
    $types .= "i";
}

if ($selected_place > 0) {
    $sql .= " AND o.place_id = ?";
    $params[] = $selected_place;
    $types .= "i";
}

if ($selected_shift > 0) {
    $sql .= " AND o.shift_id = ?";
    $params[] = $selected_shift;
    $types .= "i";
}

$sql .= " ORDER BY o.plant_id, o.place_id, o.shift_id, o.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders_result = $stmt->get_result();

// Hitung statistik overall
$total_orders = $orders_result->num_rows;

// Query untuk statistik per hari berdasarkan filter
$stats_sql = "SELECT 
    SUM(makan_senin) as total_senin,
    SUM(makan_selasa) as total_selasa,
    SUM(makan_rabu) as total_rabu,
    SUM(makan_kamis) as total_kamis,
    SUM(makan_jumat) as total_jumat,
    SUM(makan_sabtu) as total_sabtu,
    SUM(makan_minggu) as total_minggu,
    SUM(kupon_senin) as kupon_senin,
    SUM(kupon_selasa) as kupon_selasa,
    SUM(kupon_rabu) as kupon_rabu,
    SUM(kupon_kamis) as kupon_kamis,
    SUM(kupon_jumat) as kupon_jumat,
    SUM(kupon_sabtu) as kupon_sabtu,
    SUM(kupon_minggu) as kupon_minggu
FROM orders 
WHERE 1=1";

$stats_params = [];
$stats_types = "";

if ($selected_week > 0) {
    $stats_sql .= " AND week_id = ?";
    $stats_params[] = $selected_week;
    $stats_types .= "i";
}

if ($selected_plant > 0) {
    $stats_sql .= " AND plant_id = ?";
    $stats_params[] = $selected_plant;
    $stats_types .= "i";
}

if ($selected_place > 0) {
    $stats_sql .= " AND place_id = ?";
    $stats_params[] = $selected_place;
    $stats_types .= "i";
}

if ($selected_shift > 0) {
    $stats_sql .= " AND shift_id = ?";
    $stats_params[] = $selected_shift;
    $stats_types .= "i";
}

$stats_stmt = $conn->prepare($stats_sql);
if ($stats_params) {
    $stats_stmt->bind_param($stats_types, ...$stats_params);
}
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();

// Query untuk summary per lokasi dan shift
$summary_sql = "SELECT 
    p.name as plant_name,
    pl.name as place_name,
    s.nama_shift as shift_name,
    SUM(makan_senin) as senin,
    SUM(makan_selasa) as selasa,
    SUM(makan_rabu) as rabu,
    SUM(makan_kamis) as kamis,
    SUM(makan_jumat) as jumat,
    SUM(makan_sabtu) as sabtu,
    SUM(makan_minggu) as minggu,
    COUNT(*) as total_orders
FROM orders o
JOIN plant p ON o.plant_id = p.id
JOIN place pl ON o.place_id = pl.id
LEFT JOIN shift s ON o.shift_id = s.id
WHERE 1=1";

$summary_params = [];
$summary_types = "";

if ($selected_week > 0) {
    $summary_sql .= " AND o.week_id = ?";
    $summary_params[] = $selected_week;
    $summary_types .= "i";
}

$summary_sql .= " GROUP BY o.plant_id, o.place_id, o.shift_id 
                  ORDER BY p.name, pl.name, s.nama_shift";

$summary_stmt = $conn->prepare($summary_sql);
if ($summary_params) {
    $summary_stmt->bind_param($summary_types, ...$summary_params);
}
$summary_stmt->execute();
$summary_result = $summary_stmt->get_result();
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title>Data Order Makan - ParaCanteen</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <style>
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #696cff;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #696cff;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .day-stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
            border-top: 3px solid #696cff;
        }
        .day-stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #696cff;
        }
        .day-stat-label {
            color: #6c757d;
            font-size: 0.8rem;
        }
        .meal-status {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .meal-yes {
            background: #28a745;
            color: white;
        }
        .meal-no {
            background: #dc3545;
            color: white;
        }
        .meal-kupon {
            background: #ffc107;
            color: black;
        }
        .meal-libur {
            background: #6c757d;
            color: white;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #696cff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        .summary-badge {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .table-summary th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php include 'layout/sidebar.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php include 'layout/navbar.php'; ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Vendor /</span> Data Order Makan
              </h4>

              <!-- Filter Section -->
              <div class="card mb-4">
                <div class="card-header">
                  <h5 class="mb-0"><i class="bx bx-filter"></i> Filter Data</h5>
                </div>
                <div class="card-body">
                  <form method="GET" action="">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label for="week_id" class="form-label">Week</label>
                          <select class="form-select" name="week_id" id="week_id">
                            <option value="0">Semua Week</option>
                            <?php 
                            $weeks_result->data_seek(0);
                            while($week = $weeks_result->fetch_assoc()): ?>
                              <option value="<?= $week['id']; ?>" <?= ($week['id']==$selected_week) ? 'selected' : ''; ?>>
                                Week <?= $week['week_number']; ?>
                              </option>
                            <?php endwhile; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label for="plant_id" class="form-label">Plant</label>
                          <select class="form-select" name="plant_id" id="plant_id">
                            <option value="0">Semua Plant</option>
                            <?php 
                            $plants_result->data_seek(0);
                            while($plant = $plants_result->fetch_assoc()): ?>
                              <option value="<?= $plant['id']; ?>" <?= ($plant['id']==$selected_plant) ? 'selected' : ''; ?>>
                                <?= $plant['name']; ?>
                              </option>
                            <?php endwhile; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label for="place_id" class="form-label">Tempat</label>
                          <select class="form-select" name="place_id" id="place_id">
                            <option value="0">Semua Tempat</option>
                            <?php 
                            $places_result->data_seek(0);
                            while($place = $places_result->fetch_assoc()): ?>
                              <option value="<?= $place['id']; ?>" <?= ($place['id']==$selected_place) ? 'selected' : ''; ?>>
                                <?= $place['name']; ?>
                              </option>
                            <?php endwhile; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <label for="shift_id" class="form-label">Shift</label>
                          <select class="form-select" name="shift_id" id="shift_id">
                            <option value="0">Semua Shift</option>
                            <?php 
                            $shifts_result->data_seek(0);
                            while($shift = $shifts_result->fetch_assoc()): ?>
                              <option value="<?= $shift['id']; ?>" <?= ($shift['id']==$selected_shift) ? 'selected' : ''; ?>>
                                <?= $shift['nama_shift']; ?>
                              </option>
                            <?php endwhile; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                          <i class="bx bx-search"></i> Terapkan Filter
                        </button>
                        <a href="data-makan.php" class="btn btn-outline-secondary">
                          <i class="bx bx-reset"></i> Reset
                        </a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Statistik Overall -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <div class="stat-card">
                    <div class="stat-number"><?= $total_orders ?></div>
                    <div class="stat-label">Total Order</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="stat-card">
                    <div class="stat-number"><?= array_sum([$stats['total_senin'], $stats['total_selasa'], $stats['total_rabu'], $stats['total_kamis'], $stats['total_jumat'], $stats['total_sabtu'], $stats['total_minggu']]) ?></div>
                    <div class="stat-label">Total Makan</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="stat-card">
                    <div class="stat-number"><?= array_sum([$stats['kupon_senin'], $stats['kupon_selasa'], $stats['kupon_rabu'], $stats['kupon_kamis'], $stats['kupon_jumat'], $stats['kupon_sabtu'], $stats['kupon_minggu']]) ?></div>
                    <div class="stat-label">Total Kupon</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="stat-card">
                    <div class="stat-number">Week <?= $selected_week ?: 'All' ?></div>
                    <div class="stat-label">Week Aktif</div>
                  </div>
                </div>
              </div>

              <!-- Statistik Per Hari -->
              <div class="card mb-4">
                <div class="card-header">
                  <h5 class="mb-0"><i class="bx bx-stats"></i> Statistik Per Hari</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-2">
                      <div class="day-stat-card">
                        <div class="day-stat-number"><?= $stats['total_senin'] ?? 0 ?></div>
                        <div class="day-stat-label">Senin</div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="day-stat-card">
                        <div class="day-stat-number"><?= $stats['total_selasa'] ?? 0 ?></div>
                        <div class="day-stat-label">Selasa</div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="day-stat-card">
                        <div class="day-stat-number"><?= $stats['total_rabu'] ?? 0 ?></div>
                        <div class="day-stat-label">Rabu</div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="day-stat-card">
                        <div class="day-stat-number"><?= $stats['total_kamis'] ?? 0 ?></div>
                        <div class="day-stat-label">Kamis</div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="day-stat-card">
                        <div class="day-stat-number"><?= $stats['total_jumat'] ?? 0 ?></div>
                        <div class="day-stat-label">Jumat</div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="day-stat-card">
                        <div class="day-stat-number"><?= $stats['total_sabtu'] ?? 0 ?></div>
                        <div class="day-stat-label">Sabtu</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Summary Per Lokasi & Shift -->
              <div class="card mb-4">
                <div class="card-header">
                  <h5 class="mb-0"><i class="bx bx-map"></i> Summary Per Lokasi & Shift</h5>
                </div>
                <div class="card-body">
                  <?php if ($summary_result->num_rows > 0): ?>
                    <div class="table-responsive">
                      <table class="table table-bordered table-summary">
                        <thead>
                          <tr>
                            <th>Plant</th>
                            <th>Tempat</th>
                            <th>Shift</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                            <th>Sabtu</th>
                            <th>Minggu</th>
                            <th>Total Order</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while($summary = $summary_result->fetch_assoc()): ?>
                            <tr>
                              <td><strong><?= $summary['plant_name'] ?></strong></td>
                              <td><?= $summary['place_name'] ?></td>
                              <td><?= $summary['shift_name'] ?: 'All' ?></td>
                              <td>
                                <?php if ($summary['senin'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['senin'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($summary['selasa'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['selasa'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($summary['rabu'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['rabu'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($summary['kamis'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['kamis'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($summary['jumat'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['jumat'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($summary['sabtu'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['sabtu'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($summary['minggu'] > 0): ?>
                                  <span class="badge bg-primary summary-badge"><?= $summary['minggu'] ?></span>
                                <?php else: ?>
                                  <span class="text-muted">0</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <span class="badge bg-success"><?= $summary['total_orders'] ?></span>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="text-center py-4">
                      <i class="bx bx-map" style="font-size: 48px; color: #dee2e6;"></i>
                      <p class="mt-2 text-muted">Tidak ada data summary untuk filter yang dipilih.</p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Data Table Detail -->
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bx bx-food-menu"></i> Data Order Detail</h5>
                  <span class="badge bg-primary"><?= $total_orders ?> Order</span>
                </div>
                <div class="card-body">
                  <?php if ($orders_result->num_rows > 0): ?>
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>User</th>
                            <th>Lokasi</th>
                            <th>Shift</th>
                            <th>Week</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                            <th>Sabtu</th>
                            <th>Minggu</th>
                            <th>Tanggal Order</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $orders_result->data_seek(0);
                          while($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="user-avatar">
                                    <?= strtoupper(substr($order['nama'], 0, 2)) ?>
                                  </div>
                                  <div>
                                    <div class="fw-bold"><?= htmlspecialchars($order['nama']) ?></div>
                                    <small class="text-muted"><?= $order['nip'] ?></small>
                                  </div>
                                </div>
                              </td>
                              <td>
                                <small>
                                  <strong><?= $order['plant_name'] ?></strong><br>
                                  <?= $order['place_name'] ?>
                                </small>
                              </td>
                              <td>
                                <?= $order['shift_name'] ?: '<span class="text-muted">-</span>' ?>
                              </td>
                              <td>
                                <span class="badge bg-primary">Week <?= $order['week_number'] ?></span>
                              </td>
                              <td>
                                <?php if ($order['makan_senin'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_senin'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_senin'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($order['makan_selasa'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_selasa'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_selasa'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($order['makan_rabu'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_rabu'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_rabu'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($order['makan_kamis'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_kamis'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_kamis'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($order['makan_jumat'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_jumat'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_jumat'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($order['makan_sabtu'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_sabtu'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_sabtu'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($order['makan_minggu'] == 1): ?>
                                  <span class="meal-status meal-yes" title="Makan">M</span>
                                <?php elseif ($order['kupon_minggu'] == 1): ?>
                                  <span class="meal-status meal-kupon" title="Kupon">K</span>
                                <?php elseif ($order['libur_minggu'] == 1): ?>
                                  <span class="meal-status meal-libur" title="Libur">L</span>
                                <?php else: ?>
                                  <span class="meal-status meal-no" title="Tidak">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <small><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="text-center py-5">
                      <i class="bx bx-package" style="font-size: 64px; color: #dee2e6;"></i>
                      <h5 class="mt-3">Tidak ada data order</h5>
                      <p class="text-muted">Tidak ditemukan data order untuk filter yang dipilih.</p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Legend -->
              <div class="card mt-4">
                <div class="card-body">
                  <h6 class="mb-3">Keterangan Status:</h6>
                  <div class="row">
                    <div class="col-auto">
                      <span class="meal-status meal-yes me-2">M</span> Makan
                    </div>
                    <div class="col-auto">
                      <span class="meal-status meal-kupon me-2">K</span> Kupon
                    </div>
                    <div class="col-auto">
                      <span class="meal-status meal-libur me-2">L</span> Libur
                    </div>
                    <div class="col-auto">
                      <span class="meal-status meal-no me-2">-</span> Tidak
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  © <script>document.write(new Date().getFullYear());</script>,
                  Part of <a href="#" class="footer-link fw-bolder">ParagonCorp</a>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <script>
        // Auto submit form ketika filter berubah
        document.getElementById('week_id').addEventListener('change', function() {
            this.form.submit();
        });
        document.getElementById('plant_id').addEventListener('change', function() {
            this.form.submit();
        });
        document.getElementById('place_id').addEventListener('change', function() {
            this.form.submit();
        });
        document.getElementById('shift_id').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
  </body>
</html>

<?php
// Tutup koneksi
if (isset($conn) && $conn) {
    $conn->close();
}
?>