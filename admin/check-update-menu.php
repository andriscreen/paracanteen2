<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Check Update Menu | ParaCanteen</title>
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
</head>
<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php include 'layout/sidebar.php'; ?>
      <!-- / Menu -->
      <div class="layout-page">
        <!-- Navbar -->
        <?php include 'layout/navbar.php'; ?>
        <!-- / Navbar -->
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container mt-4">
            <div class="card shadow-sm p-4">
              <h4 class="mb-4"><i class="bi bi-calendar-week"></i> Tabel Menu</h4>
              
              <!-- Form untuk filter berdasarkan week_id -->
              <form method="GET" class="mb-4">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <label for="week_id" class="form-label">Filter Week</label>
                    <select name="week_id" id="week_id" class="form-select">
                      <option value="">Semua Week</option>
                      <?php
                      // Menampilkan daftar week_id untuk filter
                      $weekQuery = "SELECT DISTINCT week_id FROM menu";
                      $weekResult = $conn->query($weekQuery);
                      while ($row = $weekResult->fetch_assoc()) {
                        $selected = isset($_GET['week_id']) && $_GET['week_id'] == $row['week_id'] ? 'selected' : '';
                        echo "<option value='" . $row['week_id'] . "' $selected>Week " . $row['week_id'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                  </div>
                </div>
              </form>

              <!-- Tabel Menu -->
              <div class="table-responsive">
                <table class="table table-bordered align-middle mb-3">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Week</th>
                      <th scope="col">Day</th>
                      <th scope="col">Menu Name</th>
                      <th scope="col">Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Ambil input dari form
                    $week_id = isset($_GET['week_id']) ? $_GET['week_id'] : '';

                    // Query untuk mengambil data menu dengan filter week_id
                    $sql = "SELECT week_id, day, menu_name, keterangan FROM menu WHERE 1";

                    if ($week_id !== '') {
                      $sql .= " AND week_id = ?";
                    }

                    $stmt = $conn->prepare($sql);

                    // Bind parameter untuk filter week_id
                    if ($week_id !== '') {
                      $stmt->bind_param('i', $week_id);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['week_id'] . "</td>
                                <td>" . $row['day'] . "</td>
                                <td>" . $row['menu_name'] . "</td>
                                <td>" . ($row['keterangan'] ? $row['keterangan'] : 'Tidak ada keterangan') . "</td>
                              </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='4' class='text-center'>Tidak ada data</td></tr>";
                    }

                    $conn->close();
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- / Content -->
          
          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">
                © <script>document.write(new Date().getFullYear());</script>, Part of <a href="#" target="_blank" class="footer-link fw-bolder">ParagonCorp</a>
              </div>
            </div>
          </footer>
          <!-- / Footer -->
        </div>
        <!-- / Content wrapper -->
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
  <!-- Page JS -->
  <script src="../assets/js/dashboards-analytics.js"></script>
</body>
</html>
