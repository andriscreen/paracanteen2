<?php include "../auth.php"; ?>
<?php if ($_SESSION['role'] !== 'admin') { header("Location: ../form_login.php"); exit; } ?>
<?php include 'config/db.php'; 
// Ambil data department aktif
$query = "SELECT DISTINCT id, week_number 
          FROM week 
          ORDER BY week_number ASC";
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

    <title>Update Menu | ParaCanteen</title>

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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Update Menu</h4>

              <!-- Basic Layout -->
              <div class="row">
              <!-- Form Tambah Menu -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add/Update Menu</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/menu_action.php" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="action" value="add">
                      
                      <div class="mb-3">
                        <label class="form-label">Week</label>
                        <select name="week_id" class="form-select" required>
                          <option value="">-- Select Week --</option>
                          <?php
                            $weeks = mysqli_query($conn, "SELECT id, week_number 
                                                        FROM week 
                                                        ORDER BY week_number ASC");
                            while($week = mysqli_fetch_assoc($weeks)) {
                              echo "<option value='{$week['id']}'>Week {$week['week_number']}</option>";
                            }
                          ?>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Day</label>
                        <select name="day" class="form-select" required>
                          <option value="">-- Select Day --</option>
                          <option value="Senin">Senin</option>
                          <option value="Selasa">Selasa</option>
                          <option value="Rabu">Rabu</option>
                          <option value="Kamis">Kamis</option>
                          <option value="Jumat">Jumat</option>
                          <option value="Sabtu">Sabtu</option>
                          <option value="Minggu">Minggu</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Menu Name</label>
                        <input type="text" name="menu_name" class="form-control" placeholder="Enter menu name" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Enter keterangan" required>
                      </div>
                      

                      <div class="mb-3">
                        <label class="form-label">Menu Image</label>
                        <input type="file" name="menu_image" class="form-control" accept="image/*" required>
                        <small class="text-muted">Upload image for the menu (JPG, PNG). Will replace existing image if any.</small>
                      </div>

                      <button type="submit" class="btn btn-primary">Save Menu</button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Form Hapus Menu -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Delete Menu</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/menu_action.php" method="POST">
                      <input type="hidden" name="action" value="delete">

                      <div class="mb-3">
                        <label class="form-label">Week</label>
                        <select name="week_id" class="form-select" id="deleteWeekSelect" required>
                          <option value="">-- Select Week --</option>
                          <?php
                            $weeks = mysqli_query($conn, "SELECT DISTINCT m.week_id, w.week_number 
                                                        FROM menu m 
                                                        JOIN week w ON m.week_id = w.id 
                                                        ORDER BY w.week_number ASC");
                            while($week = mysqli_fetch_assoc($weeks)) {
                              echo "<option value='{$week['week_id']}'>Week {$week['week_number']}</option>";
                            }
                          ?>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Select Menu</label>
                        <select name="id" class="form-select" id="menuSelect" required>
                          <option value="">-- Select Menu First --</option>
                        </select>
                      </div>

                      <button type="submit" class="btn btn-danger">Delete Menu</button>
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

    <!-- Menu selection script -->
    <script>
    document.getElementById('deleteWeekSelect').addEventListener('change', function() {
        const weekId = this.value;
        const menuSelect = document.getElementById('menuSelect');
        
        // Reset menu select
        menuSelect.innerHTML = '<option value="">-- Select Menu --</option>';
        
        if (weekId) {
            // Fetch menus for selected week
            fetch('execution/get_menus.php?week_id=' + weekId)
                .then(response => response.json())
                .then(menus => {
                    menus.forEach(menu => {
                        const option = document.createElement('option');
                        option.value = menu.id;
                        option.textContent = `${menu.day} - ${menu.menu_name}`;
                        menuSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });
    </script>
  </body>
</html>
