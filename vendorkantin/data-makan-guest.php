<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'vendorkantin') { 
    header("Location: ../form_login.php"); 
    exit; 
} ?>
<?php include 'config/db.php'; ?>

<?php
// Get vendor info
$vendor_id = $_SESSION['user_id'];
$vendor_query = "SELECT nama_vendor FROM vendorkantin WHERE id = ?";
$stmt = $conn->prepare($vendor_query);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$vendor_result = $stmt->get_result();
$vendor = $vendor_result->fetch_assoc();
$vendor_name = $vendor['nama_vendor'];

// Get current week
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

// Get guest orders for current week
$guest_orders_query = "
    SELECT 
        og.*,
        g.nama as guest_name,
        g.gmail,
        p.name as plant_name,
        pl.name as place_name,
        w.week_number,
        y.year_value
    FROM order_guest og
    JOIN guest g ON og.guest_id = g.id
    JOIN week w ON og.week_id = w.id
    JOIN year y ON og.year_id = y.id
    JOIN plant p ON og.plant_id = p.id
    JOIN place pl ON og.place_id = pl.id
    WHERE og.week_id = ?
    ORDER BY og.created_at DESC
";
$stmt = $conn->prepare($guest_orders_query);
$stmt->bind_param("i", $current_week['week_id']);
$stmt->execute();
$guest_orders_result = $stmt->get_result();

// Process data for summary
$daily_summary = [
    'senin' => 0,
    'selasa' => 0,
    'rabu' => 0,
    'kamis' => 0,
    'jumat' => 0,
    'sabtu' => 0,
    'minggu' => 0
];

$plant_summary = [];
$place_summary = [];
$total_guests = 0;
$total_meals = 0;

$guest_orders = [];
while($order = $guest_orders_result->fetch_assoc()) {
    $guest_orders[] = $order;
    $total_guests++;
    
    // Count daily meals
    foreach ($daily_summary as $day => $count) {
        if ($order["makan_$day"] == 1) {
            $daily_summary[$day]++;
            $total_meals++;
        }
    }
    
    // Count by plant
    $plant_name = $order['plant_name'];
    if (!isset($plant_summary[$plant_name])) {
        $plant_summary[$plant_name] = 0;
    }
    $plant_summary[$plant_name]++;
    
    // Count by place
    $place_name = $order['place_name'];
    if (!isset($place_summary[$place_name])) {
        $place_summary[$place_name] = 0;
    }
    $place_summary[$place_name]++;
}

// Get day names in Indonesian
$day_names = [
    'senin' => 'Senin',
    'selasa' => 'Selasa',
    'rabu' => 'Rabu',
    'kamis' => 'Kamis',
    'jumat' => 'Jumat',
    'sabtu' => 'Sabtu',
    'minggu' => 'Minggu'
];

// Get current day
$current_day = strtolower(date('l'));
$day_mapping = [
    'monday' => 'senin',
    'tuesday' => 'selasa',
    'wednesday' => 'rabu',
    'thursday' => 'kamis',
    'friday' => 'jumat',
    'saturday' => 'sabtu',
    'sunday' => 'minggu'
];
$today_indonesia = $day_mapping[$current_day] ?? 'senin';
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

    <title>Data Makan Guest - Vendor Kantin</title>
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
        .summary-card {
            background: linear-gradient(135deg, #696cff 0%, #8592a3 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .day-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .day-card.today {
            border: 2px solid #10b981;
            background-color: #f0fdf4;
        }
        .day-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .meal-count {
            font-size: 24px;
            font-weight: bold;
            color: #696cff;
        }
        .guest-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .day-badge {
            display: inline-flex;
            align-items: center;
            margin: 2px;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
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
        .today-badge {
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #696cff, #8592a3);
            border-radius: 4px;
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
                <span class="text-muted fw-light">Vendor Kantin /</span> Data Makan Guest
              </h4>

              <!-- Week Information -->
              <div class="summary-card">
                <div class="row">
                  <div class="col-md-4">
                    <h4 class="text-white mb-1">Week <?= $current_week['week_number'] ?></h4>
                    <p class="text-white mb-0">Tahun <?= $current_week['year_value'] ?></p>
                    <small class="text-white-50"><?= date('d F Y') ?></small>
                  </div>
                  <div class="col-md-4 text-center">
                    <h2 class="text-white mb-1"><?= $total_guests ?></h2>
                    <p class="text-white mb-0">Total Guest</p>
                  </div>
                  <div class="col-md-4 text-end">
                    <h2 class="text-white mb-1"><?= $total_meals ?></h2>
                    <p class="text-white mb-0">Total Pesanan Makan</p>
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- Daily Summary -->
                <div class="col-lg-8">
                  <div class="card mb-4">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-calendar me-2"></i>
                        Summary Harian
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <?php foreach ($daily_summary as $day => $count): 
                            $is_today = $day === $today_indonesia;
                            $day_name = $day_names[$day];
                        ?>
                        <div class="col-md-4 mb-3">
                          <div class="day-card <?= $is_today ? 'today' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                              <div>
                                <h6 class="mb-1"><?= $day_name ?></h6>
                                <?php if ($is_today): ?>
                                  <span class="today-badge">Hari Ini</span>
                                <?php endif; ?>
                              </div>
                              <div class="meal-count"><?= $count ?></div>
                            </div>
                            <div class="progress-bar">
                              <div class="progress-fill" style="width: <?= min(($count / max($total_guests, 1)) * 100, 100) ?>%"></div>
                            </div>
                            <small class="text-muted">
                              <?= $count ?> dari <?= $total_guests ?> guest
                            </small>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>

                  <!-- Guest Orders List -->
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2"></i>
                        Detail Pesanan Guest
                      </h5>
                    </div>
                    <div class="card-body">
                      <?php if (count($guest_orders) > 0): ?>
                        <div class="table-responsive">
                          <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>Nama Guest</th>
                                <th>Email</th>
                                <th>Plant</th>
                                <th>Tempat</th>
                                <th>Hari Makan</th>
                                <th>Total</th>
                                <th>Tanggal Pesan</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($guest_orders as $order): 
                                  $total_days = (
                                      $order['makan_senin'] + $order['makan_selasa'] + 
                                      $order['makan_rabu'] + $order['makan_kamis'] + 
                                      $order['makan_jumat'] + $order['makan_sabtu'] + 
                                      $order['makan_minggu']
                                  );
                              ?>
                              <tr>
                                <td>
                                  <strong><?= htmlspecialchars($order['guest_name']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($order['gmail']) ?></td>
                                <td>
                                  <span class="badge bg-primary"><?= htmlspecialchars($order['plant_name']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($order['place_name']) ?></td>
                                <td>
                                  <?php foreach ($day_names as $key => $name): 
                                      $is_active = $order["makan_$key"] == 1;
                                  ?>
                                  <span class="day-badge <?= $is_active ? 'day-active' : 'day-inactive' ?>">
                                    <?= substr($name, 0, 3) ?>
                                    <?php if ($is_active): ?>
                                      <i class='bx bx-check ms-1'></i>
                                    <?php else: ?>
                                      <i class='bx bx-x ms-1'></i>
                                    <?php endif; ?>
                                  </span>
                                  <?php endforeach; ?>
                                </td>
                                <td>
                                  <strong><?= $total_days ?> hari</strong>
                                </td>
                                <td>
                                  <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                  </small>
                                </td>
                              </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      <?php else: ?>
                        <div class="text-center py-4">
                          <i class='bx bx-calendar-x fs-1 text-muted mb-3'></i>
                          <h5 class="text-muted">Belum Ada Pesanan Guest</h5>
                          <p class="text-muted">Tidak ada guest yang memesan untuk week ini.</p>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="col-lg-4">
                  <!-- Today's Summary -->
                  <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                      <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-restaurant me-2"></i>
                        Pesanan Hari Ini
                      </h5>
                    </div>
                    <div class="card-body text-center">
                      <h1 class="display-4 text-success mb-2"><?= $daily_summary[$today_indonesia] ?></h1>
                      <p class="mb-1"><strong><?= $day_names[$today_indonesia] ?></strong></p>
                      <p class="text-muted mb-0">Total makan hari ini</p>
                      <div class="mt-3">
                        <small class="text-muted">
                          <?= number_format(($daily_summary[$today_indonesia] / max($total_guests, 1)) * 100, 1) ?>% dari total guest
                        </small>
                      </div>
                    </div>
                  </div>

                  <!-- Plant Distribution -->
                  <div class="card mb-4">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-building me-2"></i>
                        Distribusi per Plant
                      </h5>
                    </div>
                    <div class="card-body">
                      <?php if (count($plant_summary) > 0): ?>
                        <?php foreach ($plant_summary as $plant => $count): ?>
                          <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                              <span class="fw-medium"><?= htmlspecialchars($plant) ?></span>
                              <span class="badge bg-primary"><?= $count ?></span>
                            </div>
                            <div class="progress-bar">
                              <div class="progress-fill" style="width: <?= ($count / $total_guests) * 100 ?>%"></div>
                            </div>
                            <small class="text-muted">
                              <?= number_format(($count / $total_guests) * 100, 1) ?>%
                            </small>
                          </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <p class="text-muted text-center mb-0">Tidak ada data</p>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Place Distribution -->
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-map me-2"></i>
                        Distribusi per Tempat
                      </h5>
                    </div>
                    <div class="card-body">
                      <?php if (count($place_summary) > 0): ?>
                        <?php foreach ($place_summary as $place => $count): ?>
                          <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                              <span class="fw-medium"><?= htmlspecialchars($place) ?></span>
                              <span class="badge bg-info"><?= $count ?></span>
                            </div>
                            <div class="progress-bar">
                              <div class="progress-fill" style="width: <?= ($count / $total_guests) * 100 ?>%"></div>
                            </div>
                            <small class="text-muted">
                              <?= number_format(($count / $total_guests) * 100, 1) ?>%
                            </small>
                          </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <p class="text-muted text-center mb-0">Tidak ada data</p>
                      <?php endif; ?>
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

    <script>
      // Auto refresh every 5 minutes to get latest data
      setTimeout(function() {
        window.location.reload();
      }, 300000); // 5 minutes

      // Print function for daily summary
      function printDailySummary() {
        window.print();
      }

      // Today's orders highlight
      document.addEventListener('DOMContentLoaded', function() {
        const todayCard = document.querySelector('.day-card.today');
        if (todayCard) {
          todayCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    </script>
  </body>
</html>