<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; 
// Ambil data department aktif
$query = "SELECT id, name FROM department WHERE is_active = 1 ORDER BY name ASC";
$result = mysqli_query($conn, $query);
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

    <title>Update place Name | ParaCanteen</title>

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
        <?php include_once __DIR__ . '/layout/sidebar.php'; ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php include_once __DIR__ . '/layout/navbar.php'; ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Update Place Name</h4>

              <!-- Basic Layout -->
              <div class="row">
              <!-- Form Tambah Place -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Place</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/place_action.php" method="POST">
                      <input type="hidden" name="action" value="add">

                      <div class="mb-3">
                        <label class="form-label">Plant</label>
                        <select name="plant_id" class="form-select" required>
                          <option value="">-- Select Plant --</option>
                          <?php
                            $plants = mysqli_query($conn, "SELECT id, name FROM plant ORDER BY name ASC");
                            while($p = mysqli_fetch_assoc($plants)) {
                              echo "<option value='{$p['id']}'>{$p['name']}</option>";
                            }
                          ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Place Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter place name" required>
                      </div>

                      <button type="submit" class="btn btn-primary">Add Place</button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Form Hapus Place -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Delete Place</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/place_action.php" method="POST">
                      <input type="hidden" name="action" value="delete">

                      <div class="mb-3">
                        <label class="form-label">Select Place</label>
                        <select name="id" class="form-select" required>
                          <option value="">-- Select Place --</option>
                          <?php
                            // tampilkan dropdown place dari database
                            $result = mysqli_query($conn, "SELECT id, name FROM place ORDER BY name ASC");
                            while($row = mysqli_fetch_assoc($result)) {
                              echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                          ?>
                        </select>
                      </div>

                      <button type="submit" class="btn btn-danger">Delete Place</button>
                    </form>
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
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
