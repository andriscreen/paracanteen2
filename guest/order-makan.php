<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'guest') { 
    header("Location: ../form_login.php"); 
    exit; 
} ?>
<?php include 'config/db.php'; ?>

<?php
// Get current week and year - FIXED QUERY
$current_week_query = "
    SELECT w.id as week_id, w.week_number, y.id as year_id, y.year_value
    FROM week w
    JOIN year y ON w.year_id = y.id
    WHERE w.week_number = WEEK(CURDATE(), 3)  -- Mode 3: ISO week (Monday as first day, week 1-53)
    AND y.year_value = YEAR(CURDATE())
    LIMIT 1
";
$current_week_result = $conn->query($current_week_query);

if ($current_week_result->num_rows > 0) {
    $current_week = $current_week_result->fetch_assoc();
} else {
    // Fallback: get the latest week from database
    $fallback_query = "
        SELECT w.id as week_id, w.week_number, y.id as year_id, y.year_value
        FROM week w
        JOIN year y ON w.year_id = y.id
        ORDER BY y.year_value DESC, w.week_number DESC
        LIMIT 1
    ";
    $fallback_result = $conn->query($fallback_query);
    $current_week = $fallback_result->fetch_assoc();
}

// Get next week - FIXED QUERY
$next_week_query = "
    SELECT w.id as week_id, w.week_number, y.id as year_id, y.year_value
    FROM week w
    JOIN year y ON w.year_id = y.id
    WHERE w.week_number = WEEK(CURDATE(), 3) + 1
    AND y.year_value = YEAR(CURDATE())
    LIMIT 1
";
$next_week_result = $conn->query($next_week_query);
$next_week = $next_week_result->fetch_assoc();

// If next week not found in current year, get first week of next year
if (!$next_week && $next_week_result->num_rows === 0) {
    $next_year_query = "
        SELECT w.id as week_id, w.week_number, y.id as year_id, y.year_value
        FROM week w
        JOIN year y ON w.year_id = y.id
        WHERE w.week_number = 1
        AND y.year_value = YEAR(CURDATE()) + 1
        LIMIT 1
    ";
    $next_year_result = $conn->query($next_year_query);
    $next_week = $next_year_result->fetch_assoc();
}

// Debug info (bisa dihapus setelah testing)
error_log("Current Week Number from DB: " . $current_week['week_number']);
error_log("Current Date: " . date('Y-m-d'));
error_log("PHP Week: " . date('W'));
error_log("MySQL WEEK() mode 0: " . $conn->query("SELECT WEEK(CURDATE(), 0) as week")->fetch_assoc()['week']);
error_log("MySQL WEEK() mode 1: " . $conn->query("SELECT WEEK(CURDATE(), 1) as week")->fetch_assoc()['week']);
error_log("MySQL WEEK() mode 3: " . $conn->query("SELECT WEEK(CURDATE(), 3) as week")->fetch_assoc()['week']);

// Get plants and places
$plants = $conn->query("SELECT * FROM plant ORDER BY name");
$places = $conn->query("SELECT * FROM place ORDER BY name");

// Check if guest already has order for current week
$guest_id = $_SESSION['user_id'];
$existing_order_query = "
    SELECT * FROM order_guest 
    WHERE guest_id = ? AND week_id = ?
    ORDER BY created_at DESC 
    LIMIT 1
";
$stmt = $conn->prepare($existing_order_query);
$stmt->bind_param("ii", $guest_id, $current_week['week_id']);
$stmt->execute();
$existing_order_result = $stmt->get_result();
$existing_order = $existing_order_result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $week_id = $_POST['week_id'];
    $year_id = $_POST['year_id'];
    $plant_id = $_POST['plant_id'];
    $place_id = $_POST['place_id'];
    
    // Get day selections
    $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    $day_values = [];
    foreach ($days as $day) {
        $day_values["makan_$day"] = isset($_POST["makan_$day"]) ? 1 : 0;
    }
    
    // Check if at least one day is selected
    $total_days_selected = array_sum($day_values);
    if ($total_days_selected === 0) {
        $_SESSION['error'] = "Pilih setidaknya satu hari untuk makan!";
    } else {
        // Insert or update order
        if ($existing_order) {
            // Update existing order
            $update_query = "
                UPDATE order_guest SET 
                plant_id = ?, place_id = ?, 
                makan_senin = ?, makan_selasa = ?, makan_rabu = ?, makan_kamis = ?,
                makan_jumat = ?, makan_sabtu = ?, makan_minggu = ?, updated_at = NOW()
                WHERE id = ?
            ";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param(
                "iiiiiiiiii", 
                $plant_id, $place_id,
                $day_values['makan_senin'], $day_values['makan_selasa'], 
                $day_values['makan_rabu'], $day_values['makan_kamis'],
                $day_values['makan_jumat'], $day_values['makan_sabtu'], 
                $day_values['makan_minggu'], $existing_order['id']
            );
        } else {
            // Insert new order
            $insert_query = "
                INSERT INTO order_guest 
                (guest_id, week_id, year_id, plant_id, place_id, 
                 makan_senin, makan_selasa, makan_rabu, makan_kamis, 
                 makan_jumat, makan_sabtu, makan_minggu) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param(
                "iiiiiiiiiiii", 
                $guest_id, $week_id, $year_id, $plant_id, $place_id,
                $day_values['makan_senin'], $day_values['makan_selasa'], 
                $day_values['makan_rabu'], $day_values['makan_kamis'],
                $day_values['makan_jumat'], $day_values['makan_sabtu'], 
                $day_values['makan_minggu']
            );
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Pesanan makan berhasil " . ($existing_order ? 'diupdate' : 'disimpan') . "!";
            header("Location: order-makan.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menyimpan pesanan: " . $conn->error;
        }
    }
}
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

    <title>Order Makan - Guest ParaCanteen</title>
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
        .week-card {
            background: linear-gradient(135deg, #696cff 0%, #8592a3 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .day-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .day-option:hover {
            border-color: #696cff;
            background-color: #f8f9fa;
        }
        .day-option.selected {
            border-color: #696cff;
            background-color: #e7e7ff;
        }
        .day-checkbox {
            display: none;
        }
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .summary-day {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .summary-day:last-child {
            border-bottom: none;
        }
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 12px;
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
                <span class="text-muted fw-light">Guest /</span> Order Makan
              </h4>

              <!-- Debug Information (bisa dihapus setelah testing) -->
              <!--
              <div class="debug-info">
                <strong>Debug Info:</strong><br>
                Current Date: <?= date('Y-m-d') ?><br>
                PHP date('W'): <?= date('W') ?><br>
                Database Week: <?= $current_week['week_number'] ?><br>
                Week ID: <?= $current_week['week_id'] ?><br>
                Year: <?= $current_week['year_value'] ?>
              </div>
              -->

              <!-- Alert Messages -->
              <?php if (isset($_SESSION['success'])): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
              <?php endif; ?>

              <?php if (isset($_SESSION['error'])): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
              <?php endif; ?>

              <div class="row">
                <div class="col-lg-8">
                  <div class="card mb-4">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-calendar-edit me-2"></i>
                        Pesan Makan Mingguan
                      </h5>
                    </div>
                    <div class="card-body">
                      <!-- Week Information -->
                      <div class="week-card">
                        <div class="row">
                          <div class="col-md-6">
                            <h4 class="text-white mb-1">Week <?= $current_week['week_number'] ?></h4>
                            <p class="text-white mb-0">Tahun <?= $current_week['year_value'] ?></p>
                            <small class="text-white-50">Periode: <?= date('d M Y') ?></small>
                          </div>
                          <div class="col-md-6 text-end">
                            <p class="text-white mb-0">
                              <i class="bx bx-info-circle me-1"></i>
                              <?= $existing_order ? 'Pesanan sudah ada' : 'Belum ada pesanan' ?>
                            </p>
                          </div>
                        </div>
                      </div>

                      <form method="POST" id="orderForm">
                        <input type="hidden" name="week_id" value="<?= $current_week['week_id'] ?>">
                        <input type="hidden" name="year_id" value="<?= $current_week['year_id'] ?>">

                        <!-- Plant & Place Selection -->
                        <div class="row mb-4">
                          <div class="col-md-6">
                            <label class="form-label">Pilih Plant</label>
                            <select name="plant_id" class="form-select" required>
                              <option value="">-- Pilih Plant --</option>
                              <?php 
                              $plants->data_seek(0); // Reset pointer
                              while($plant = $plants->fetch_assoc()): ?>
                                <option value="<?= $plant['id'] ?>" 
                                  <?= ($existing_order && $existing_order['plant_id'] == $plant['id']) ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($plant['name']) ?>
                                </option>
                              <?php endwhile; ?>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Pilih Tempat</label>
                            <select name="place_id" class="form-select" required>
                              <option value="">-- Pilih Tempat --</option>
                              <?php 
                              $places->data_seek(0); // Reset pointer
                              while($place = $places->fetch_assoc()): ?>
                                <option value="<?= $place['id'] ?>" 
                                  <?= ($existing_order && $existing_order['place_id'] == $place['id']) ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($place['name']) ?>
                                </option>
                              <?php endwhile; ?>
                            </select>
                          </div>
                        </div>

                        <!-- Day Selection -->
                        <div class="mb-4">
                          <label class="form-label fw-bold">Pilih Hari Makan</label>
                          <p class="text-muted">Klik pada hari yang ingin Anda pesan makan</p>
                          
                          <div class="row">
                            <?php
                            $days = [
                                'senin' => 'Senin',
                                'selasa' => 'Selasa', 
                                'rabu' => 'Rabu',
                                'kamis' => 'Kamis',
                                'jumat' => 'Jumat',
                                'sabtu' => 'Sabtu',
                                'minggu' => 'Minggu'
                            ];
                            
                            foreach ($days as $key => $day):
                                $is_checked = $existing_order ? $existing_order["makan_$key"] : false;
                            ?>
                            <div class="col-md-4 mb-3">
                              <div class="day-option <?= $is_checked ? 'selected' : '' ?>" 
                                   onclick="toggleDay('<?= $key ?>')">
                                <input type="checkbox" 
                                       class="day-checkbox" 
                                       id="makan_<?= $key ?>" 
                                       name="makan_<?= $key ?>" 
                                       <?= $is_checked ? 'checked' : '' ?>>
                                <div>
                                  <i class='bx bx-restaurant fs-1 mb-2'></i>
                                  <h6 class="mb-1"><?= $day ?></h6>
                                  <small class="text-muted">Makan di kantin</small>
                                </div>
                              </div>
                            </div>
                            <?php endforeach; ?>
                          </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                          <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bx bx-save me-2"></i>
                            <?= $existing_order ? 'Update Pesanan' : 'Simpan Pesanan' ?>
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        <i class="bx bx-list-check me-2"></i>
                        Ringkasan Pesanan
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="order-summary">
                        <h6 class="mb-3">Week <?= $current_week['week_number'] ?></h6>
                        
                        <div id="orderSummary">
                          <?php
                          $selected_days = 0;
                          if ($existing_order) {
                              foreach ($days as $key => $day) {
                                  if ($existing_order["makan_$key"]) {
                                      $selected_days++;
                                      echo "
                                      <div class='summary-day'>
                                          <span>$day</span>
                                          <span class='badge bg-success'>✓</span>
                                      </div>";
                                  }
                              }
                          }
                          
                          if ($selected_days === 0) {
                              echo '<p class="text-muted text-center">Belum ada hari yang dipilih</p>';
                          } else {
                              echo "<div class='mt-3 p-2 bg-light rounded'>
                                      <strong>Total: $selected_days hari</strong>
                                    </div>";
                          }
                          ?>
                        </div>
                        
                        <div class="mt-4">
                          <small class="text-muted">
                            <i class='bx bx-info-circle me-1'></i>
                            Pesanan akan aktif untuk Week <?= $current_week['week_number'] ?> saja
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Information Card -->
                  <div class="card mt-4">
                    <div class="card-body">
                      <h6 class="card-title">
                        <i class="bx bx-help-circle me-2"></i>
                        Informasi
                      </h6>
                      <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                          <small class="text-muted">
                            <i class='bx bx-check me-1'></i>
                            Pilih plant dan tempat makan
                          </small>
                        </li>
                        <li class="mb-2">
                          <small class="text-muted">
                            <i class='bx bx-check me-1'></i>
                            Klik hari yang ingin dipesan
                          </small>
                        </li>
                        <li>
                          <small class="text-muted">
                            <i class='bx bx-check me-1'></i>
                            Pesanan dapat diupdate kapan saja
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

    <script>
      function toggleDay(day) {
          const checkbox = document.getElementById(`makan_${day}`);
          const dayOption = checkbox.closest('.day-option');
          
          checkbox.checked = !checkbox.checked;
          
          if (checkbox.checked) {
              dayOption.classList.add('selected');
          } else {
              dayOption.classList.remove('selected');
          }
      }

      // Form validation
      document.getElementById('orderForm').addEventListener('submit', function(e) {
          const checkboxes = document.querySelectorAll('.day-checkbox:checked');
          if (checkboxes.length === 0) {
              e.preventDefault();
              alert('Pilih setidaknya satu hari untuk makan!');
              return false;
          }
      });
    </script>
  </body>
</html>