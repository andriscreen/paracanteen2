<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'guest') { 
    header("Location: ../form_login.php"); 
    exit; 
} ?>
<?php include 'config/db.php'; ?>

<?php
$guest_id = $_SESSION['user_id'];

// Get order history for this guest - FIXED QUERY
$history_query = "
    SELECT 
        og.id,
        og.guest_id,
        og.week_id,
        og.year_id,
        og.plant_id,
        og.place_id,
        og.makan_senin,
        og.makan_selasa,
        og.makan_rabu,
        og.makan_kamis,
        og.makan_jumat,
        og.makan_sabtu,
        og.makan_minggu,
        og.created_at,
        og.updated_at,
        w.week_number,
        y.year_value,
        p.name as plant_name,
        pl.name as place_name,
        (
            (og.makan_senin = 1) + 
            (og.makan_selasa = 1) + 
            (og.makan_rabu = 1) + 
            (og.makan_kamis = 1) + 
            (og.makan_jumat = 1) + 
            (og.makan_sabtu = 1) + 
            (og.makan_minggu = 1)
        ) as total_days
    FROM order_guest og
    JOIN week w ON og.week_id = w.id
    JOIN year y ON og.year_id = y.id
    JOIN plant p ON og.plant_id = p.id
    JOIN place pl ON og.place_id = pl.id
    WHERE og.guest_id = ?
    ORDER BY og.created_at DESC
";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("i", $guest_id);
$stmt->execute();
$history_result = $stmt->get_result();

// Count statistics
$total_orders = $history_result->num_rows;
$total_days_ordered = 0;
$history_data = [];

while($order = $history_result->fetch_assoc()) {
    $history_data[] = $order;
    $total_days_ordered += $order['total_days'];
}

// Get current week order if exists
$current_week_query = "
    SELECT w.id as week_id, w.week_number, y.id as year_id, y.year_value
    FROM week w
    JOIN year y ON w.year_id = y.id
    WHERE w.week_number = WEEK(CURDATE(), 3)
    AND y.year_value = YEAR(CURDATE())
    LIMIT 1
";
$current_week_result = $conn->query($current_week_query);
$current_week = $current_week_result->fetch_assoc();

$current_order_query = "
    SELECT * FROM order_guest 
    WHERE guest_id = ? AND week_id = ?
    LIMIT 1
";
$stmt = $conn->prepare($current_order_query);
$stmt->bind_param("ii", $guest_id, $current_week['week_id']);
$stmt->execute();
$current_order_result = $stmt->get_result();
$current_order = $current_order_result->fetch_assoc();
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

    <title>Riwayat Pesanan - Guest ParaCanteen</title>
    <meta name="description" content="" />

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
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!-- Config -->
    <script src="../assets/js/config.js"></script>

    <style>
        .stat-card {
            background: linear-gradient(135deg, #696cff 0%, #8592a3 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .order-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 10px 10px 0 0;
        }
        .order-body {
            padding: 20px;
        }
        .day-badge {
            display: inline-flex;
            align-items: center;
            margin: 2px;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
        }
        .day-active {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .day-inactive {
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .current-week-badge {
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #d1d5db;
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
                <span class="text-muted fw-light">Guest /</span> Riwayat Pesanan
              </h4>

              <!-- Statistics Cards -->
              <div class="row mb-4">
                <div class="col-lg-4 col-md-6">
                  <div class="stat-card">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <i class="bx bx-calendar-check fs-1"></i>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h4 class="mb-0"><?= $total_orders ?></h4>
                        <p class="mb-0">Total Pesanan</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6">
                  <div class="stat-card">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <i class="bx bx-restaurant fs-1"></i>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h4 class="mb-0"><?= $total_days_ordered ?></h4>
                        <p class="mb-0">Total Hari Makan</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-6">
                  <div class="stat-card">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <i class="bx bx-time fs-1"></i>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h4 class="mb-0"><?= $current_order ? 'Aktif' : 'Tidak Aktif' ?></h4>
                        <p class="mb-0">Status Week Ini</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Order History -->
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    <i class="bx bx-history me-2"></i>
                    Riwayat Pesanan Makan
                  </h5>
                </div>
                <div class="card-body">
                  <?php if (count($history_data) > 0): ?>
                    <div class="row">
                      <?php foreach ($history_data as $order): 
                          $is_current_week = $order['week_number'] == $current_week['week_number'];
                          $days = [
                              'senin' => 'Sen', 'selasa' => 'Sel', 'rabu' => 'Rab', 
                              'kamis' => 'Kam', 'jumat' => 'Jum', 'sabtu' => 'Sab', 
                              'minggu' => 'Min'
                          ];
                      ?>
                      <div class="col-lg-6 mb-4">
                        <div class="order-card">
                          <div class="order-header">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <h6 class="mb-1">Week <?= $order['week_number'] ?> - <?= $order['year_value'] ?></h6>
                                <small class="text-muted">
                                  <?= date('d M Y', strtotime($order['created_at'])) ?>
                                </small>
                              </div>
                              <div>
                                <?php if ($is_current_week): ?>
                                  <span class="current-week-badge">Week Ini</span>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                          <div class="order-body">
                            <!-- Plant & Place Info -->
                            <div class="row mb-3">
                              <div class="col-6">
                                <small class="text-muted d-block">Plant</small>
                                <strong><?= htmlspecialchars($order['plant_name']) ?></strong>
                              </div>
                              <div class="col-6">
                                <small class="text-muted d-block">Tempat</small>
                                <strong><?= htmlspecialchars($order['place_name']) ?></strong>
                              </div>
                            </div>

                            <!-- Days Selection -->
                            <div class="mb-3">
                              <small class="text-muted d-block mb-2">Hari Makan:</small>
                              <div>
                                <?php foreach ($days as $key => $day_short): 
                                    $is_active = $order["makan_$key"] == 1;
                                ?>
                                <span class="day-badge <?= $is_active ? 'day-active' : 'day-inactive' ?>">
                                  <?= $day_short ?>
                                  <?php if ($is_active): ?>
                                    <i class='bx bx-check ms-1'></i>
                                  <?php else: ?>
                                    <i class='bx bx-x ms-1'></i>
                                  <?php endif; ?>
                                </span>
                                <?php endforeach; ?>
                              </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <small class="text-muted">Total: </small>
                                <strong><?= $order['total_days'] ?> hari</strong>
                              </div>
                              <div>
                                <small class="text-muted">
                                  <?= date('H:i', strtotime($order['created_at'])) ?>
                                </small>
                              </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-3">
                              <?php if ($is_current_week): ?>
                                <a href="order-makan.php" class="btn btn-sm btn-primary">
                                  <i class="bx bx-edit me-1"></i> Edit Pesanan
                                </a>
                              <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                  <i class="bx bx-time me-1"></i> Week Selesai
                                </button>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                      <i class='bx bx-calendar-x'></i>
                      <h5 class="mb-2">Belum Ada Riwayat Pesanan</h5>
                      <p class="mb-4">Anda belum pernah memesan makan melalui sistem ini.</p>
                      <a href="order-makan.php" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Buat Pesanan Pertama
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Information Card -->
              <div class="card mt-4">
                <div class="card-body">
                  <h6 class="card-title">
                    <i class="bx bx-info-circle me-2"></i>
                    Informasi Riwayat Pesanan
                  </h6>
                  <div class="row">
                    <div class="col-md-6">
                      <ul class="list-unstyled">
                        <li class="mb-2">
                          <small class="text-muted">
                            <i class='bx bx-check-circle text-success me-1'></i>
                            Pesanan week saat ini dapat diedit
                          </small>
                        </li>
                        <li class="mb-2">
                          <small class="text-muted">
                            <i class='bx bx-time text-warning me-1'></i>
                            Pesanan week sebelumnya tidak dapat diubah
                          </small>
                        </li>
                      </ul>
                    </div>
                    <div class="col-md-6">
                      <ul class="list-unstyled">
                        <li class="mb-2">
                          <small class="text-muted">
                            <i class='bx bx-calendar text-primary me-1'></i>
                            Riwayat disimpan secara permanen
                          </small>
                        </li>
                        <li>
                          <small class="text-muted">
                            <i class='bx bx-restaurant text-info me-1'></i>
                            Setiap week perlu pesan ulang
                          </small>
                        </li>
                      </ul>
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
                  ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  , Part of
                  <a href="#" target="_blank" class="footer-link fw-bolder">ParagonCorp</a>
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

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
  </body>
</html>