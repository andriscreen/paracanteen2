<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; ?>
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

    <title>Check Update User | ParaCanteen</title>

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

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
,        <?php include 'layout/sidebar.php'; ?>
        
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php include 'layout/navbar.php'; ?>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container mt-4">
                <div class="card shadow-sm p-4">
                    <h4 class="mb-4"><i class="bi bi-calendar-week"></i> Tabel User</h4>
                    
                    <!-- Form untuk filter dan pencarian -->
                    <form method="GET" class="mb-4">
                        <!-- Filter Departemen -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="departemen" class="form-label">Filter Departemen</label>
                                <select name="departemen" id="departemen" class="form-select">
                                    <option value="">Semua Departemen</option>
                                    <?php
                                    // Menampilkan daftar departemen untuk filter
                                    $departemenQuery = "SELECT DISTINCT departemen FROM users";
                                    $departemenResult = $conn->query($departemenQuery);
                                    while ($row = $departemenResult->fetch_assoc()) {
                                        $selected = isset($_GET['departemen']) && $_GET['departemen'] == $row['departemen'] ? 'selected' : '';
                                        echo "<option value='" . $row['departemen'] . "' $selected>" . $row['departemen'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari Nama</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Cari berdasarkan nama" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel User -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Nama</th>
                                    <th scope="col">NIP</th>
                                    <th scope="col">Gmail</th>
                                    <th scope="col">Departemen</th>
                                    <th scope="col">Total Kupon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Ambil input dari form
                                $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
                                $departemen = isset($_GET['departemen']) ? $_GET['departemen'] : '';

                                // Query untuk mengambil data user dengan filter
                                $sql = "SELECT nama, nip, gmail, departemen, total_kupon FROM users WHERE nama LIKE ?";

                                if ($departemen !== '') {
                                    $sql .= " AND departemen = ?";
                                }

                                $stmt = $conn->prepare($sql);

                                // Bind parameter untuk pencarian nama dan departemen
                                if ($departemen !== '') {
                                    $stmt->bind_param('ss', $search, $departemen);
                                } else {
                                    $stmt->bind_param('s', $search);
                                }

                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>" . $row['nama'] . "</td>
                                                <td>" . $row['nip'] . "</td>
                                                <td>" . $row['gmail'] . "</td>
                                                <td>" . $row['departemen'] . "</td>
                                                <td>" . $row['total_kupon'] . "</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Tidak ada data</td></tr>";
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
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
