<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Check Update Data | ParaCanteen</title>
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
          <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
              <!-- Table Vendor Name -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-3">
                                <h5 class="m-0 me-2">Table Vendor Name</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-3">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Nama Vendor</th>
                                            <th>Gmail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql_vendor = "SELECT nama, nama_vendor, gmail FROM vendorkantin";
                                        $res_vendor = $conn->query($sql_vendor);
                                        if ($res_vendor && $res_vendor->num_rows > 0) {
                                            while ($row = $res_vendor->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>{$row['nama']}</td>
                                                        <td>{$row['nama_vendor']}</td>
                                                        <td>{$row['gmail']}</td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center'>Tidak ada data</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Department Name -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-3">
                                <h5 class="m-0 me-2">Table Departement Name</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Department Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql_dept = "SELECT name FROM department WHERE is_active = 1";
                                        $res_dept = $conn->query($sql_dept);
                                        if ($res_dept && $res_dept->num_rows > 0) {
                                            while ($row = $res_dept->fetch_assoc()) {
                                                echo "<tr><td>{$row['name']}</td></tr>";
                                            }
                                        } else {
                                            echo "<tr><td class='text-center'>Tidak ada data</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Place (Plant Name & Place Name) -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-3">
                                <h5 class="m-0 me-2">Table Place (Plant & Place)</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Plant Name</th>
                                            <th>Place Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Ambil nama plant dan nama place dengan JOIN
                                        $sql_place = "SELECT place.name AS place_name, plant.name AS plant_name 
                                                      FROM place 
                                                      INNER JOIN plant ON place.plant_id = plant.id";
                                        $res_place = $conn->query($sql_place);

                                        if ($res_place && $res_place->num_rows > 0) {
                                            while ($row = $res_place->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>" . htmlspecialchars($row['plant_name']) . "</td>
                                                        <td>" . htmlspecialchars($row['place_name']) . "</td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='2' class='text-center'>Tidak ada data</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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
