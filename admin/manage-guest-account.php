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

    <title>Manage Guest Account | ParaCanteen</title>

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

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Guest Account /</span> Manage Guest Account</h4>

              <!-- Basic Layout -->
              <div class="row">
              <!-- Form Tambah Akun Guest -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Guest Account</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/guest_action.php" method="POST">
                      <input type="hidden" name="action" value="add">

                      <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="nama" class="form-control" placeholder="Guest Name" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                          type="email"
                          name="gmail"
                          class="form-control"
                          placeholder="guest@example.com"
                          required
                          pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                          title="Masukkan alamat email yang valid, misalnya: nama@example.com"
                        >
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                      </div>

                      <button type="submit" class="btn btn-primary">Add Guest Account</button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Form Hapus Akun Guest -->
              <div class="col-xl">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Delete Guest Account</h5>
                  </div>
                  <div class="card-body">
                    <form action="execution/guest_action.php" method="POST">
                      <input type="hidden" name="action" value="delete">

                      <div class="mb-3">
                        <label class="form-label">Select Guest Account</label>
                        <select name="id" class="form-select" required>
                          <option value="">-- Select Guest Account --</option>
                          <?php
                            // tampilkan dropdown akun guest dari database
                            $result = mysqli_query($conn, "SELECT id, nama, gmail FROM guest ORDER BY nama ASC");
                            while($row = mysqli_fetch_assoc($result)) {
                              echo "<option value='{$row['id']}'>{$row['nama']} ({$row['gmail']})</option>";
                            }
                          ?>
                        </select>
                      </div>

                      <button type="submit" class="btn btn-danger">Delete Guest Account</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Daftar Guest Accounts -->
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Guest Accounts List</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Created At</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $guests = mysqli_query($conn, "
                        SELECT id, nama, gmail, is_active, created_at 
                        FROM guest 
                        ORDER BY created_at DESC
                      ");
                      
                      while($guest = mysqli_fetch_assoc($guests)) {
                        $status = $guest['is_active'] == 1 ? 
                          '<span class="badge bg-success">Active</span>' : 
                          '<span class="badge bg-danger">Inactive</span>';
                          
                        echo "
                        <tr>
                          <td>{$guest['id']}</td>
                          <td>{$guest['nama']}</td>
                          <td>{$guest['gmail']}</td>
                          <td>{$status}</td>
                          <td>" . date('d M Y H:i', strtotime($guest['created_at'])) . "</td>
                        </tr>";
                      }
                      
                      if (mysqli_num_rows($guests) == 0) {
                        echo '<tr><td colspan="5" class="text-center text-muted py-4">No guest accounts found</td></tr>';
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
    <script src="../assets/js/main.js"></script>
  </body>
</html>